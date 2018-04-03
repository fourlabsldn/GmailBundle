<?php

namespace FL\GmailBundle\Services;

use FL\GmailBundle\Factory\GmailMessageFactory;
use FL\GmailBundle\Factory\GmailLabelFactory;
use FL\GmailBundle\Event\GmailSyncMessagesEvent;
use FL\GmailBundle\Model\Collection\GmailMessageCollection;
use FL\GmailBundle\Model\Collection\GmailLabelCollection;
use FL\GmailBundle\Model\GmailIdsInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * 1. Resolves Messages and their Labels
 * 2. Informs of this, by dispatching a @see GmailSyncMessagesEvent.
 */
class SyncMessages
{
    /**
     * @var Email
     */
    private $email;

    /**
     * @var OAuth
     */
    private $oAuth;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var GmailMessageFactory
     */
    private $gmailMessageFactory;

    /**
     * @var GmailLabelFactory
     */
    private $gmailLabelFactory;

    /**
     * This is used to ensure a user's message isn't retrieved from the API twice.
     *
     * Keys are $userId
     * Values are $messageId.
     *
     * @var string[]
     */
    private $apiMessageCache = [];

    /**
     * This is used to ensure a user's list of labels isn't retrieved from the API twice.
     *
     * $this->apiLabelCache[$userId][$labelId] = $labelName.
     *
     * @var array
     */
    private $apiLabelCache = [];

    /**
     * Keys are $userId.
     *
     * @var GmailMessageCollection[]
     */
    private $gmailMessageCache = [];

    /**
     * Keys are $userId.
     *
     * @var GmailLabelCollection[]
     */
    private $gmailLabelCache = [];

    /**
     * SyncManager constructor.
     *
     * @param Email                    $email
     * @param OAuth                    $oAuth
     * @param EventDispatcherInterface $dispatcher
     * @param GmailMessageFactory      $gmailMessageFactory
     * @param GmailLabelFactory        $gmailLabelFactory
     */
    public function __construct(
        Email $email,
        OAuth $oAuth,
        EventDispatcherInterface $dispatcher,
        GmailMessageFactory $gmailMessageFactory,
        GmailLabelFactory $gmailLabelFactory
    ) {
        $this->email = $email;
        $this->dispatcher = $dispatcher;
        $this->oAuth = $oAuth;
        $this->gmailMessageFactory = $gmailMessageFactory;
        $this->gmailLabelFactory = $gmailLabelFactory;
    }

    /**
     * Processes emails using the API based on given $gmailIds.
     *
     * @param GmailIdsInterface $gmailIds
     */
    public function syncFromGmailIds(GmailIdsInterface $gmailIds)
    {
        $userId = $gmailIds->getUserId();

        // Initialize $this->apiMessageCache for this user
        if (!array_key_exists($userId, $this->apiMessageCache)) {
            $this->apiMessageCache[$userId] = [];
        }
        // Initialize $this->apiLabelCache for this user
        // And populate it right away.
        if (!array_key_exists($userId, $this->apiLabelCache)) {
            $this->apiLabelCache[$userId] = [];
            foreach ($this->email->getLabels($userId) as $label) {
                $this->apiLabelCache[$userId][$label->id] = $label->name;
            }
        }
        // Initialize $this->gmailMessageCache for this user
        if (!array_key_exists($userId, $this->gmailMessageCache)) {
            $this->gmailMessageCache[$userId] = new GmailMessageCollection();
        }
        // Initialize $this->gmailLabelCache for this user
        if (!array_key_exists($userId, $this->gmailLabelCache)) {
            $this->gmailLabelCache[$userId] = new GmailLabelCollection();
        }

        $gmailIdsNotInCache = [];
        foreach ($gmailIds->getGmailIds() as $id) {
            if (!in_array($id, $this->apiMessageCache[$userId])) {
                $this->apiMessageCache[$userId][] = $id;
                $gmailIdsNotInCache[] = $id;
            }
        }

        $batchResponse = $this->email->getBatch($userId, $gmailIdsNotInCache) ?? [];
        foreach ($batchResponse->getFoundApiMessages() as $apiMessage) {
            $this->processApiMessage($userId, $this->oAuth->resolveDomain(), $apiMessage);
        }
        // Mark all emails as processed, including those that weren't found.
        // E.g. gmailIds that have now been deleted in gmail.
        $processedGmailIds = $batchResponse->getAllGmailIdsRequested();

        $syncEvent = new GmailSyncMessagesEvent($this->gmailMessageCache[$userId], $this->gmailLabelCache[$userId], $processedGmailIds, $userId);
        $this->dispatcher->dispatch(GmailSyncMessagesEvent::EVENT_NAME, $syncEvent);
    }

    /**
     * @param string                        $userId
     * @param string                        $domain
     * @param \Google_Service_Gmail_Message $apiMessage
     */
    private function processApiMessage(string $userId, string $domain, \Google_Service_Gmail_Message $apiMessage)
    {
        /** @var \Google_Service_Gmail_MessagePart $payload */
        $payload = $apiMessage->getPayload();
        /** @var \Google_Service_Gmail_MessagePartHeader[] $headers */
        $headers = $payload->getHeaders() ?? [];

        // Messages with nonexistent, null, or empty From/To headers should not be saved.
        $headerNames = [];
        foreach ($headers as $header) {
            if (
                ('From' === $header->getName() || 'To' === $header->getName()) &&
                (null === $header->getValue() || '' === $header->getValue())
            ) {
                return;
            }
            $headerNames[] = $header->getName();
        }
        if (
            (!in_array('From', $headerNames)) ||
            (!in_array('To', $headerNames))
        ) {
            return;
        }

        $gmailLabelsInMessage = [];

        // Populate $this->gmailLabelCache[$userId] (which is a GmailLabelsCollection)
        // A label with the same name, belonging to the same user will not be converted to a GmailLabel more than once.
        // That way two messages with the same label, have the same instance of that GmailLabel.
        foreach ($this->labelNames($userId, $apiMessage) as $labelName) {
            if (!$this->gmailLabelCache[$userId]->hasLabelOfName($labelName)) {
                $this->gmailLabelCache[$userId]->addLabel($this->gmailLabelFactory->createFromProperties($labelName, $domain, $userId));
            }

            $gmailLabelsInMessage[] = $this->gmailLabelCache[$userId]->getLabelOfName($labelName);
        }

        // Populate $this->gmailMessageCache[$userId] (which is a GmailMessageCollection)
        $this->gmailMessageCache[$userId]->addMessage($this->gmailMessageFactory->createFromGmailApiMessage($apiMessage, $gmailLabelsInMessage, $userId, $domain));
    }

    /**
     * @param string                        $userId
     * @param \Google_Service_Gmail_Message $apiMessage
     *
     * @return string[]
     */
    private function labelNames(string $userId, \Google_Service_Gmail_Message $apiMessage)
    {
        $labelNames = [];
        if (is_array($labelIds = $apiMessage->getLabelIds())) { // $labelIds might be null
            $labelNames = array_filter($this->apiLabelCache[$userId], function ($labelName, $labelId) use ($labelIds) {
                return in_array($labelId, $labelIds);
            }, ARRAY_FILTER_USE_BOTH);
        }

        return $labelNames;
    }
}
