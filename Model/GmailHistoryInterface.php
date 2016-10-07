<?php

namespace FL\GmailBundle\Model;

/**
 * Interface GmailHistoryInterface
 * @package FL\GmailBundle\Model
 */
interface GmailHistoryInterface
{
    /**
     * Set the user ID.
     * @param string $userId
     * @return GmailHistoryInterface
     */
    public function setUserId(string $userId): GmailHistoryInterface;

    /**
     * Get the user ID.
     * @return string|null
     */
    public function getUserId();

    /**
     * Set the history ID.
     * @param int $historyId
     * @return GmailHistoryInterface
     */
    public function setHistoryId(int $historyId): GmailHistoryInterface;

    /**
     * Get the history ID.
     * @return int|null
     */
    public function getHistoryId();
}
