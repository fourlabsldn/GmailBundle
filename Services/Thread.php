<?php

namespace FL\GmailBundle\Services;

/**
 * This service communicates with thread
 * related methods in @see Google_Service_Gmail.
 */
class Thread
{
    /**
     * @var GoogleServices
     */
    private $googleServices;

    /**
     * @param GoogleServices $googleClients
     */
    public function __construct(
        GoogleServices $googleClients
    ) {
        $this->googleServices = $googleClients;
    }

    /**
     * @param string $userId
     * @param string $threadId
     *
     * @return \Google_Service_Gmail_Thread|null (null if the thread no longer exists in GMail)
     *
     * @throws \Google_Service_Exception
     */
    public function archive(string $userId, string $threadId): \Google_Service_Gmail_Thread
    {
        $postBody = new \Google_Service_Gmail_ModifyThreadRequest();
        $postBody->setRemoveLabelIds(['INBOX']);

        return $this->modifyThread($userId, $threadId, $postBody);
    }

    /**
     * @param string $userId
     * @param string $threadId
     *
     * @return \Google_Service_Gmail_Thread|null (null if the thread no longer exists in GMail)
     *
     * @throws \Google_Service_Exception
     */
    public function markRead(string $userId, string $threadId): \Google_Service_Gmail_Thread
    {
        $postBody = new \Google_Service_Gmail_ModifyThreadRequest();
        $postBody->setRemoveLabelIds(['UNREAD']);

        return $this->modifyThread($userId, $threadId, $postBody);
    }

    /**
     * @param string $userId
     * @param string $threadId
     *
     * @return \Google_Service_Gmail_Thread|null (null if the thread no longer exists in GMail)
     *
     * @throws \Google_Service_Exception
     */
    public function markUnread(string $userId, string $threadId): \Google_Service_Gmail_Thread
    {
        $postBody = new \Google_Service_Gmail_ModifyThreadRequest();
        $postBody->setAddLabelIds(['UNREAD']);

        return $this->modifyThread($userId, $threadId, $postBody);
    }

    /**
     * @param string                                    $userId
     * @param string                                    $threadId
     * @param \Google_Service_Gmail_ModifyThreadRequest $postBody
     *
     * @return \Google_Service_Gmail_Thread|null
     *
     * @throws \Google_Service_Exception
     */
    private function modifyThread(string $userId, string $threadId, \Google_Service_Gmail_ModifyThreadRequest $postBody)
    {
        try {
            return $this->googleServices->getGoogleServiceGmailForUserId($userId)->users_threads->modify($userId, $threadId, $postBody);
        } catch (\Google_Service_Exception $exception) {
            // thread does not exist
            if (404 === $exception->getCode()) {
                return;
            }

            throw $exception;
        }
    }
}
