<?php

namespace FL\GmailBundle\Model;

/**
 * Interface GmailIdsInterface
 * @package FL\GmailBundle\Model
 */
interface GmailIdsInterface
{
    /**
     * Set the user ID.
     * @param string $userId
     * @return GmailIdsInterface
     */
    public function setUserId(string $userId): GmailIdsInterface;

    /**
     * Get the user ID.
     * @return string|null
     */
    public function getUserId();

    /**
     * Set the gmail IDs.
     * By convention, place latestIds first
     * @param string[]|null $gmailIds
     * @return GmailIdsInterface
     */
    public function setGmailIds(array $gmailIds = null): GmailIdsInterface;

    /**
     * Get the gmail IDs.
     * @return string[]|null
     */
    public function getGmailIds();
}