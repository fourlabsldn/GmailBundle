<?php

namespace FL\GmailBundle\Model;

/**
 * Concrete classes help you persist which gmail message ids are in need of sync,
 * for a userId, in a domain, when syncing with Gmail.
 *
 * @see https://developers.google.com/gmail/api/v1/reference/users/messages/get
 */
interface GmailIdsInterface
{
    /**
     * @param string $userId
     *
     * @return GmailIdsInterface
     */
    public function setUserId(string $userId): self;

    /**
     * @return string
     */
    public function getUserId(): string;

    /**
     * @param string $domain
     *
     * @return GmailIdsInterface
     */
    public function setDomain(string $domain): self;

    /**
     * @return string
     */
    public function getDomain(): string;

    /**
     * By convention, place latestIds first in the array.
     * Make sure to filter out repeat gmailIds when implementing this method.
     *
     * @param string[] $gmailIds
     *
     * @return GmailIdsInterface
     */
    public function setGmailIds(array $gmailIds): self;

    /**
     * The limit parameter allows you to retrieve a slice of the gmailIds.
     * If the parameter is null, all  gmailIds are returned.
     *
     * @param int $limit
     *
     * @return string[]
     */
    public function getGmailIds(int $limit = null): array;
}
