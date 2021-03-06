<?php

namespace FL\GmailBundle\Model;

/**
 * Concrete classes abstract Google Apps Users.
 * They are not meant to be persisted.
 *
 * @see https://developers.google.com/admin-sdk/directory/v1/guides/manage-users
 */
interface GmailUserInterface
{
    /**
     * @return string
     */
    public function getUserId(): string;

    /**
     * @param string $userId
     *
     * @return GmailUserInterface
     */
    public function setUserId(string $userId): self;

    /**
     * @return string
     */
    public function getPrimaryEmailAddress(): string;

    /**
     * @param string $primaryEmailAddress
     *
     * @return GmailUserInterface
     */
    public function setPrimaryEmailAddress(string $primaryEmailAddress): self;

    /**
     * @return string[]
     */
    public function getEmailAliases(): array;

    /**
     * @param string $emailAlias
     *
     * @return GmailUserInterface
     */
    public function addEmailAlias(string $emailAlias): self;

    /**
     * @param string $emailAlias
     *
     * @return GmailUserInterface
     */
    public function removeEmailAlias(string $emailAlias): self;

    /**
     * Should return email aliases, plus primary email address.
     *
     * @return string[]
     */
    public function getAllEmailAddresses(): array;
}
