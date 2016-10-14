<?php

namespace FL\GmailBundle\Services;

/**
 * Class Thread
 * This class allows us to communicate with @see Google_Service_Gmail
 * @package FL\GmailBundle\Services
 */
class Thread
{
    /**
     * @var \Google_Service_Gmail
     */
    private $service;

    /**
     * Email constructor.
     * @param \Google_Service_Gmail $service
     */
    public function __construct(\Google_Service_Gmail $service)
    {
        $this->service = $service;
    }

    /**
     * @param string $userId
     * @param string $threadId
     * @return \Google_Service_Gmail_Thread
     */
    public function archive(string $userId, string $threadId): \Google_Service_Gmail_Thread
    {
        $postBody = new \Google_Service_Gmail_ModifyThreadRequest();
        $postBody->setRemoveLabelIds(['INBOX']);
        return $this->service->users_threads->modify($userId, $threadId, $postBody);
    }

    /**
     * @param string $userId
     * @param string $threadId
     * @return \Google_Service_Gmail_Thread
     */
    public function markRead(string $userId, string $threadId): \Google_Service_Gmail_Thread
    {
        $postBody = new \Google_Service_Gmail_ModifyThreadRequest();
        $postBody->setRemoveLabelIds(['UNREAD']);
        return $this->service->users_threads->modify($userId, $threadId, $postBody);
    }

    /**
     * @param string $userId
     * @param string $threadId
     * @return \Google_Service_Gmail_Thread
     */
    public function markUnread(string $userId, string $threadId): \Google_Service_Gmail_Thread
    {
        $postBody = new \Google_Service_Gmail_ModifyThreadRequest();
        $postBody->setAddLabelIds(['UNREAD']);
        return $this->service->users_threads->modify($userId, $threadId, $postBody);
    }
}
