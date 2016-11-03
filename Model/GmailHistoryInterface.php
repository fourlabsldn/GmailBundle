<?php

namespace FL\GmailBundle\Model;

/**
 * Interface GmailHistoryInterface.
 */
interface GmailHistoryInterface
{
    /**
     * Set the user ID.
     *
     * @param string $userId
     *
     * @return GmailHistoryInterface
     */
    public function setUserId(string $userId): GmailHistoryInterface;

    /**
     * Get the user ID.
     *
     * @return string|null
     */
    public function getUserId();

    /**
     * @param string $domain
     *
     * @return GmailHistoryInterface
     */
    public function setDomain(string $domain): GmailHistoryInterface;

    /**
     * @return string
     */
    public function getDomain(): string;

    /**
     * Set the history ID.
     *
     * @param int $historyId
     *
     * @return GmailHistoryInterface
     */
    public function setHistoryId(int $historyId): GmailHistoryInterface;

    /**
     * Get the history ID.
     *
     * @return int|null
     */
    public function getHistoryId();
}
