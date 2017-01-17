<?php

namespace FL\GmailBundle\Model;

class GmailHistory implements GmailHistoryInterface
{
    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $domain;

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
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain(string $domain): GmailHistoryInterface
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * {@inheritdoc}
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
    public function getHistoryId(): int
    {
        return $this->historyId;
    }
}
