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
 * @package FL\GmailBundle\Services
 */
class SyncManager
{
    /**
     * @var Email
     */
    private $email;

    /**
     * @var SyncManagerHelper
     */
    private $syncManagerHelper;

    /**
     * SyncManager constructor.
     * @param Email $email
     * @param SyncManagerHelper $syncManagerHelper
     */
    public function __construct(Email $email, SyncManagerHelper $syncManagerHelper)
    {
        $this->email = $email;
        $this->syncManagerHelper = $syncManagerHelper;
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
     * @param string $userId
     */
    private function fullSync(string $userId)
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

        $this->syncManagerHelper->syncFromGmailIds($userId, $gmailIds);
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
        $histories = [];

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

            foreach ($emails->getHistory() as $apiHistory) {
                $histories[] = $apiHistory;
            }

        } while (($nextPage = $emails->getNextPageToken()));

        $this->syncManagerHelper->syncFromGmailIds($userId, $histories);
    }
}
