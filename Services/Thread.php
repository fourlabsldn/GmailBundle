<?php

namespace FL\GmailBundle\Services;

/**
 * Class Thread
 * This class allows us to communicate with @see Google_Service_Gmail.
 */
class Thread
{
    /**
     * @var \Google_Service_Gmail
     */
    private $service;

    /**
     * Email constructor.
     *
     * @param \Google_Service_Gmail $service
     */
    public function __construct(\Google_Service_Gmail $service)
    {
        $this->service = $service;
    }

    /**
     * @param string $userId
     * @param string $threadId
     *
     * @return \Google_Service_Gmail_Thread|null
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
     * @return \Google_Service_Gmail_Thread|null
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
     * @return \Google_Service_Gmail_Thread|null
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
            return $this->service->users_threads->modify($userId, $threadId, $postBody);
        } catch (\Google_Service_Exception $exception) {
            // thread does not exist
            if ($exception->getCode() === 404) {
                return;
            }
            throw $exception;
        }
    }
}
