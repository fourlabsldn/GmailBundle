<?php

namespace FL\GmailBundle\Services;

use FL\GmailBundle\DataTransformer\GmailMessageTransformer;
use FL\GmailBundle\DataTransformer\GmailLabelTransformer;
use FL\GmailBundle\Event\GmailHistoryUpdatedEvent;
use FL\GmailBundle\Event\GmailSyncEndEvent;
use FL\GmailBundle\Model\Collection\GmailMessageCollection;
use FL\GmailBundle\Model\GmailHistoryInterface;
use FL\GmailBundle\Model\Collection\GmailLabelCollection;
use FL\GmailBundle\Model\GmailMessageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class SyncManager
 * 1. Fetches Messages and their Labels by using a list of Gmail Ids
 * 2. Informs of this, by dispatching a @see GmailSyncEndEvent
 * @package FL\GmailBundle\Services
 */
class SyncManager
{
    /**
     * @var Email
     */
    private $email;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var GmailMessageTransformer
     */
    private $gmailMessageTransformer;

    /**
     * @var GmailLabelTransformer
     */
    private $gmailLabelTransformer;

    /**
     * Keys are $userId
     * @var string[]
     */
    private $apiLabelCache = [];

    /**
     * Keys are $userId
     * Values are $messageId
     * @var string[]
     */
    private $apiMessageCache = [];

    /**
     * Keys are $userId
     * @var GmailLabelCollection[]
     */
    private $gmailLabelCache = [];

    /**
     * Keys are $userId
     * @var GmailMessageCollection[]
     */
    private $gmailMessageCache = [];

    /**
     * SyncManager constructor.
     * @param Email $email
     * @param EventDispatcherInterface $dispatcher
     * @param GmailMessageTransformer $gmailMessageTransformer
     * @param GmailLabelTransformer $gmailLabelTransformer
     */
    public function __construct(
        Email $email,
        EventDispatcherInterface $dispatcher,
        GmailMessageTransformer $gmailMessageTransformer,
        GmailLabelTransformer $gmailLabelTransformer
    ) {
        $this->email = $email;
        $this->dispatcher = $dispatcher;
        $this->gmailMessageTransformer = $gmailMessageTransformer;
        $this->gmailLabelTransformer = $gmailLabelTransformer;
    }

    /**
     * @param string $userId
     * @param string[] $gmailIds
     */
    public function syncFromGmailIds(string $userId, array $gmailIds)
    {
        foreach ($gmailIds as $id) {
            if (! in_array($id, $this->apiMessageCache[$userId])){
                $this->apiMessageCache[$userId][] = $id;
                $apiMessage = $this->email->getIfNotNote($userId, $id);
                if ($apiMessage instanceof \Google_Service_Gmail_Message) {
                    $this->processApiMessage($userId, $apiMessage);
                }
            }
        }
        $this->dispatchSyncEndEvent($userId);
    }

    /**
     * Get label names from the API based on given $labelIds.
     * @param string $userId
     * @param string[]|null $labelIds
     * @return string[]
     */
    private function resolveLabelNames(string $userId, array $labelIds = null)
    {
        $this->verifyCaches($userId);

        if (! is_array($labelIds)) {
            $labelIds = [];
        }

        foreach ($this->email->getLabels($userId) as $label) {
            $this->apiLabelCache[$userId][$label->id] = $label->name;
        }

        $labelNames = [];
        foreach ($labelIds as $id) {
            $labelNames[] = $this->apiLabelCache[$userId][$id];
        }

        return array_filter($this->apiLabelCache[$userId], function ($labelName, $labelId) use ($labelIds) {
            return in_array($labelId, $labelIds);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * When converting and placing a batch of $apiMessages into $allGmailMessages,
     * we must not create a new $gmailLabel, if the label's name has been used previously.
     * @param string $userId
     * @param \Google_Service_Gmail_Message $apiMessage
     */
    private function processApiMessage(string $userId, \Google_Service_Gmail_Message $apiMessage)
    {
        $this->verifyCaches($userId);

        $labelNames = $this->resolveLabelNames($userId, $apiMessage->getLabelIds());
        $gmailLabels = [];

        // populate $gmailLabels
        foreach ($labelNames as $labelName) {
            if (!$this->gmailLabelCache[$userId]->hasLabelOfName($labelName)) {
                $this->gmailLabelCache[$userId]->addLabel($this->gmailLabelTransformer->transform($labelName, $userId));
            }

            $gmailLabels[] = $this->gmailLabelCache[$userId]->getLabelOfName($labelName);
        }

        // Convert the $apiMessage, with its $gmailLabels, into a GmailMessageInterface.
        // Then add it to the $allGmailMessages collection.
        $this->gmailMessageCache[$userId]->addMessage($this->gmailMessageTransformer->transform($apiMessage, $gmailLabels, $userId));
    }

    /**
     * @param string $userId
     */
    private function verifyCaches(string $userId)
    {
        if (!array_key_exists($userId, $this->apiLabelCache)) {
            $this->apiLabelCache[$userId] = [];
        }
        if (!array_key_exists($userId, $this->apiMessageCache)) {
            $this->apiMessageCache[$userId] = [];
        }
        if (!array_key_exists($userId, $this->gmailLabelCache)) {
            $this->gmailLabelCache[$userId] = new GmailLabelCollection();
        }
        if (!array_key_exists($userId, $this->gmailMessageCache)) {
            $this->gmailMessageCache[$userId] = new GmailMessageCollection();
        }
    }

    /**
     * @param string $userId
     * @return void
     */
    private function dispatchSyncEndEvent(string $userId)
    {
        $this->verifyCaches($userId);

        /**
         * Dispatch Sync End Event
         * @var GmailHistoryInterface $history
         */
        $syncEvent = new GmailSyncEndEvent($this->gmailMessageCache[$userId], $this->gmailLabelCache[$userId]);
        $this->dispatcher->dispatch(GmailSyncEndEvent::EVENT_NAME, $syncEvent);
    }
}
