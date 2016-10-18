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
 * Class SyncHelper
 * @package FL\GmailBundle\Services
 */
class SyncHelper
{
    /**
     * @var Email
     */
    private $email;

    /**
     * SyncManager constructor.
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @param string $userId
     * @return string[]
     */
    public function resolveAllGmailIds(string $userId)
    {
        $nextPage = null;
        $historyId = null;
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

        } while (($nextPage = $apiEmailsResponse->getNextPageToken()));

        return $gmailIds;
    }

    /**
     * @param string $userId
     * @param string $currentHistoryId
     * @return string[]
     */
    public function resolveGmailIdsFromHistoryId(string $userId, string $currentHistoryId)
    {
        try {
            $gmailIds = [];
            $nextPage = null;
            do {
                /** @var \Google_Service_Gmail_ListHistoryResponse $response */
                $emails = $this->email->historyList(
                    $userId,
                    [
                        'maxResults' => 2000,
                        'pageToken' => $nextPage,
                        'startHistoryId' => $currentHistoryId,
                    ]
                );

                /** @var \Google_Service_Gmail_History $apiHistory */
                foreach ($emails->getHistory() as $apiHistory) {
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
            } while (($nextPage = $emails->getNextPageToken()));
            return $gmailIds;
        } catch (\Google_Service_Exception $e) {
            /**
             * A historyId is typically valid for at least a week, but in some rare circumstances may be valid
             * for only a few hours. If you receive an HTTP 404 error response, your application should perform
             * a full sync.
             * @link https://developers.google.com/gmail/api/v1/reference/users/history/list#startHistoryId
             */
            return $this->resolveAllGmailIds($userId);
        }
    }
}
