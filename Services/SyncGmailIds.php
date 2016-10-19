<?php

namespace FL\GmailBundle\Services;

use FL\GmailBundle\Event\GmailSyncHistoryEvent;
use FL\GmailBundle\Event\GmailSyncIdsEvent;
use FL\GmailBundle\Model\GmailHistoryInterface;
use FL\GmailBundle\Model\GmailIdsInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class SyncHelper:
 * 1. Resolves GmailIds
 * 2. Informs of this, by dispatching a @see GmailSyncIdsEvent
 * 3. Informs how caught up we are with historyIds, by dispatching @see GmailSyncHistoryEvent
 * @package FL\GmailBundle\Services
 */
class SyncGmailIds
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
     * @var string
     */
    private $historyClass;

    /**
     * @var string
     */
    private $gmailIdsClass;

    /**
     * SyncManager constructor.
     * @param Email $email
     * @param EventDispatcherInterface $dispatcher
     * @param string $historyClass
     * @param string $gmailIdsClass
     */
    public function __construct(Email $email, EventDispatcherInterface $dispatcher, string $historyClass, string $gmailIdsClass)
    {
        $this->email = $email;
        $this->dispatcher = $dispatcher;
        $this->historyClass = $historyClass;
        $this->gmailIdsClass = $gmailIdsClass;
    }

    /**
     * @param string $userId
     */
    public function syncAll(string $userId)
    {
        $nextPage = null;
        $gmailIds = [];

        do {
            $apiEmailsResponse = $this->email->list(
                $userId,
                [
                    'maxResults' => 2000,
                    'includeSpamTrash' => 'true',
                    'pageToken' => $nextPage,
                ]
            );

            foreach ($apiEmailsResponse as $idAndThreadId) {
                $gmailIds[] = $idAndThreadId['id'];
            }

            // We need to get the History ID from the very first message in the first batch
            // so we can know up to which point the sync has been done for this user.
            if ( (!isset($historyId)) && count($apiEmailsResponse) > 0) {
                /** @var \Google_Service_Gmail_Message $latestMessage */
                $latestMessage = $apiEmailsResponse[0];
                $latestMessage = $this->email->get($userId, $latestMessage->getId()); // populates historyId (one extra API call)
                $newHistoryId = $latestMessage->getHistoryId();
            }
        } while (($nextPage = $apiEmailsResponse->getNextPageToken()));
        if (count($gmailIds) > 0) {
            $this->dispatchGmailIdsEvent($userId, $gmailIds);
        }
        if (isset($newHistoryId)) {
            $this->dispatchHistoryEvent($userId, intval($newHistoryId));
        }
    }

    /**
     * @param string $userId
     * @param int $currentHistoryId
     */
    public function syncFromHistoryId(string $userId, int $currentHistoryId)
    {
        try {
            $gmailIds = [];
            $nextPage = null;
            do {
                /** @var \Google_Service_Gmail_ListHistoryResponse $response */
                $historyList = $this->email->historyList(
                    $userId,
                    [
                        'maxResults' => 2000,
                        'pageToken' => $nextPage,
                        'startHistoryId' => $currentHistoryId,
                    ]
                );

                /** @var \Google_Service_Gmail_History $apiHistory */
                foreach ($historyList->getHistory() as $apiHistory) {
                    $histories[] = $apiHistory;
                    /**
                     * @var \Google_Service_Gmail_Message $historyMessage
                     *
                     * @see \Google_Service_Gmail_History::getMessages() does not make another API call
                     * @link https://developers.google.com/gmail/api/v1/reference/users/history/list?authuser=1
                     */
                    foreach ($apiHistory->getMessages() as $historyMessage) {
                        $gmailIds[] = $historyMessage->getId();
                    }
                }
                // We need to get the History ID in the first batch
                // so we can know up to which point the sync has been done for this user.
                if (! isset($newHistoryId)) {
                    $newHistoryId = $historyList->getHistoryId();
                }
            } while (($nextPage = $historyList->getNextPageToken()));
            if (count($gmailIds) > 0) {
                $this->dispatchGmailIdsEvent($userId, $gmailIds);
            }
            if (isset($newHistoryId)) {
                $this->dispatchHistoryEvent($userId, intval($newHistoryId));
            }
        } catch (\Google_Service_Exception $e) {
            /**
             * A historyId is typically valid for at least a week, but in some rare circumstances may be valid
             * for only a few hours. If you receive an HTTP 404 error response, your application should perform
             * a full sync.
             * @link https://developers.google.com/gmail/api/v1/reference/users/history/list#startHistoryId
             */
            $this->syncAll($userId);
        }
    }

    /**
     * @param string $userId
     * @param int $historyId
     */
    private function dispatchHistoryEvent(string $userId, int $historyId)
    {
        /**
         * Dispatch History Event
         * @var GmailHistoryInterface $history
         */
        $history = new $this->historyClass;
        $history->setUserId($userId)->setHistoryId($historyId);
        $historyEvent = new GmailSyncHistoryEvent($history);
        $this->dispatcher->dispatch(GmailSyncHistoryEvent::EVENT_NAME, $historyEvent);
    }

    /**
     * @param string $userId
     * @param string[] $gmailIdsArray
     */
    private function dispatchGmailIdsEvent(string $userId, array $gmailIdsArray)
    {
        /**
         * Dispatch GmailIds Event
         * @var GmailIdsInterface $gmailIdsObject
         */
        $gmailIdsObject = new $this->gmailIdsClass;
        $gmailIdsObject->setUserId($userId)->setGmailIds($gmailIdsArray);
        $gmailIdsEvent = new GmailSyncIdsEvent($gmailIdsObject);
        $this->dispatcher->dispatch(GmailSyncIdsEvent::EVENT_NAME, $gmailIdsEvent);
    }
}
