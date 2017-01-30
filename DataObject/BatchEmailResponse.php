<?php

namespace FL\GmailBundle\DataObject;

class BatchEmailResponse
{
    /**
     * @var \Google_Service_Gmail_Message[]
     */
    private $foundApiMessages;

    /**
     * @var int[]
     */
    private $allGmailIdsRequested;

    /**
     * @param \Google_Service_Gmail_Message[] $foundApiMessages
     * @param int[]                           $allGmailIdsRequested
     */
    public function __construct(
        array $foundApiMessages,
        array $allGmailIdsRequested
    ) {
        $this->foundApiMessages = $foundApiMessages;
        $this->allGmailIdsRequested = $allGmailIdsRequested;
    }

    /**
     * @return \Google_Service_Gmail_Message[]
     */
    public function getFoundApiMessages(): array
    {
        return $this->foundApiMessages;
    }

    /**
     * @return \int[]
     */
    public function getAllGmailIdsRequested(): array
    {
        return $this->allGmailIdsRequested;
    }
}