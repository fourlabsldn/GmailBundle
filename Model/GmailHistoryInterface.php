<?php

namespace FL\GmailBundle\Model;

/**
 * Concrete classes help you persist the latest Gmail historyId,
 * for a userId, in a domain, when syncing with Gmail.
 *
 * @see https://developers.google.com/gmail/api/guides/sync#partial_synchronization
 */
interface GmailHistoryInterface
{
    /**
     * @param string $userId
     *
     * @return GmailHistoryInterface
     */
    public function setUserId(string $userId): GmailHistoryInterface;

    /**
     * @return string
     */
    public function getUserId(): string;

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
     * @param int $historyId
     *
     * @return GmailHistoryInterface
     */
    public function setHistoryId(int $historyId): GmailHistoryInterface;

    /**
     * @return int
     */
    public function getHistoryId(): int;
}
