<?php

namespace FL\GmailBundle\Model;

/**
 * Class GmailHistory
 * Latest Gmail history ID for the user, set when syncing with Gmail.
 * @package FL\GmailBundle\Model
 */
class GmailHistory implements GmailHistoryInterface
{
    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $domain = '';

    /**
     * @var string[]|null
     */
    protected $gmailIds = [];

    /**
     * @var int
     */
    protected $historyId;

    /**
     * {@inheritdoc}
     */
    public function setUserId(string $userId): GmailHistoryInterface
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @inheritdoc
     */
    public function setDomain(string $domain): GmailHistoryInterface
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * {@inheritdoc}
     */
    public function setHistoryId(int $historyId): GmailHistoryInterface
    {
        $this->historyId = $historyId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHistoryId()
    {
        return $this->historyId;
    }
}
