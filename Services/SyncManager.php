<?php

namespace FL\GmailBundle\Services;

use FL\GmailBundle\DataTransformer\GmailMessageTransformer;
use FL\GmailBundle\DataTransformer\GmailLabelTransformer;
use FL\GmailBundle\Event\GmailHistoryUpdatedEvent;
use FL\GmailBundle\Event\GmailSyncEndEvent;
use FL\GmailBundle\Model\Collection\GmailMessageCollection;
use FL\GmailBundle\Model\GmailHistoryInterface;
use FL\GmailBundle\Model\Collection\GmailLabelCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class SyncManager
 * This class relies on @see Email, to provide a wrapper
 * which we can use to send emails through the Gmail API.
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
     * @var string $historyClass
     */
    private $historyClass;

    /**
     * Max number of results when getting emails from Gmail.
     * @var int
     */
    private $maxResults;

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
     * @param string $historyClass
     * @param int $maxResults
     */
    public function __construct(
        Email $email,
        EventDispatcherInterface $dispatcher,
        GmailMessageTransformer $gmailMessageTransformer,
        GmailLabelTransformer $gmailLabelTransformer,
        string $historyClass,
        int $maxResults
    ) {
        $this->email = $email;
        $this->dispatcher = $dispatcher;
        $this->gmailMessageTransformer = $gmailMessageTransformer;
        $this->gmailLabelTransformer = $gmailLabelTransformer;
        $this->historyClass = $historyClass;
        $this->maxResults = $maxResults;
    }

    /**
     * @param string $userId
     * @param string|null $historyId
     */
    public function sync(string $userId, string $historyId = null)
    {
        if ($historyId) {
            try {
                $this->partialSync($userId, $historyId);
            } catch (\Google_Service_Exception $e) {
                /**
                 * A historyId is typically valid for at least a week, but in some rare circumstances may be valid
                 * for only a few hours. If you receive an HTTP 404 error response, your application should perform
                 * a full sync.
                 * @link https://developers.google.com/gmail/api/v1/reference/users/history/list#startHistoryId
                 */
                $this->fullSync($userId);
            }
        } else {
            $this->fullSync($userId);
        }
    }

    /**
     * Run full sync of stored messages against the user's Gmail Messages.
     * Save the historyId for future partial sync.
     * This is done as a batch operation using a number of results defined in maxResults.
     * @param string $userId
     * @return void
     */
    private function fullSync(string $userId)
    {
        $nextPage = null;
        $historyId = null;

        do {
            $apiEmailsResponse = $this->email->list(
                $userId,
                [
                    'maxResults' => $this->maxResults,
                    'includeSpamTrash' => 'true',
                    'pageToken' => $nextPage,
                ]
            );

            $apiMessages = $this->email->removeNotes($userId, $apiEmailsResponse->getMessages());
            foreach ($apiMessages as $apiMessage) {
                $this->processApiMessage($userId, $apiMessage);
            }

            // We need to get the History ID from the very first message in the first batch
            // so we can know up to which point the sync has been done for this user.
            if (!$historyId && count($apiMessages) > 0) {
                /** @var \Google_Service_Gmail_Message $latestMessage */
                $latestMessage = $apiMessages[0];
                $historyId = $latestMessage->getHistoryId();
            }
        } while (($nextPage = $apiEmailsResponse->getNextPageToken()));

        if (!is_null($historyId)) {
            $this->dispatchSyncEvent($userId);
            $this->dispatchHistoryEvent($userId, $historyId);
        }
    }

    /**
     * Run partial sync of stored messages against the user's Gmail Messages.
     * Save the newHistoryId for future partial sync.
     * @param string $userId
     * @param string $currentHistoryId
     * @return void
     */
    private function partialSync(string $userId, string $currentHistoryId)
    {
        $nextPage = null;
        $newHistoryId = null;

        do {
            /** @var \Google_Service_Gmail_ListHistoryResponse $response */
            $emails = $this->email->historyList(
                $userId,
                [
                    'maxResults' => $this->maxResults,
                    'pageToken' => $nextPage,
                    'startHistoryId' => $currentHistoryId,
                ]
            );

            foreach ($emails->getHistory() as $apiHistory) {
                $this->processApiHistory($userId, $apiHistory);
            }

            if (!$newHistoryId) {
                $newHistoryId = $emails->getHistoryId();
            }
        } while (($nextPage = $emails->getNextPageToken()));

        if (!is_null($newHistoryId)) {
            $this->dispatchSyncEvent($userId);
            $this->dispatchHistoryEvent($userId, $newHistoryId);
        }
    }

    /**
     * Get label names from the API based on given $labelIds.
     * @param string $userId
     * @param string[] $labelIds
     * @return string[]
     */
    private function resolveLabelNames(string $userId, array $labelIds)
    {
        $this->verifyCaches($userId);

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
     * Before we can use an $apiHistory, we need to get its $apiMessages.
     * And then use @see SyncManager::processApiMessage on each $apiMessage
     * @param string $userId
     * @param \Google_Service_Gmail_History $apiHistory
     */
    private function processApiHistory(string $userId, \Google_Service_Gmail_History $apiHistory)
    {
        $this->verifyCaches($userId);

        /** @var \Google_Service_Gmail_Message $historyMessage */
        foreach ($apiHistory->getMessages() as $historyMessage) {
            $historyMessageId = $historyMessage->getId();
            if (! in_array($historyMessageId, $this->apiMessageCache[$userId])){
                $this->apiMessageCache[$userId][] = $historyMessageId;
                /** @var \Google_Service_Gmail_Message $apiMessage */
                $apiMessage = $this->email->get($userId, $historyMessage->getId());
                $this->processApiMessage($userId, $apiMessage);
            }
        }
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
    private function dispatchSyncEvent(string $userId)
    {
        $this->verifyCaches($userId);

        /**
         * Dispatch Sync End Event
         * @var GmailHistoryInterface $history
         */
        $syncEvent = new GmailSyncEndEvent($this->gmailMessageCache[$userId], $this->gmailLabelCache[$userId]);
        $this->dispatcher->dispatch(GmailSyncEndEvent::EVENT_NAME, $syncEvent);
    }

    /**
     * @param string $userId
     * @param string $historyId
     * @return void
     */
    private function dispatchHistoryEvent(string $userId, string $historyId)
    {
        $this->verifyCaches($userId);

        /**
         * Dispatch History Update Event
         * @var GmailHistoryInterface $history
         */
        $history = new $this->historyClass;
        $history->setUserId($userId)->setHistoryId($historyId);
        $historyEvent = new GmailHistoryUpdatedEvent($history);
        $this->dispatcher->dispatch(GmailHistoryUpdatedEvent::EVENT_NAME, $historyEvent);
    }
}
