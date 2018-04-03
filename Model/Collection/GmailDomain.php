<?php

namespace FL\GmailBundle\Model\Collection;

use FL\GmailBundle\Model\GmailUserInterface;

/**
 * Abstraction of a Google Apps Domain:
 * It consists of a string, for the account's domain
 * and a list of GmailUsers.
 *
 * This class is not meant to be persisted.
 */
class GmailDomain
{
    /**
     * @var \SplObjectStorage
     */
    private $gmailUsers;

    /**
     * @var string
     */
    private $domain;

    /**
     * LabelCollection constructor.
     *
     * @param string
     */
    public function __construct(string $domain)
    {
        $this->domain = $domain;
        $this->gmailUsers = new \SplObjectStorage();
    }

    /**
     * @param GmailUserInterface $user
     *
     * @return GmailDomain
     */
    public function addGmailUser(GmailUserInterface $user): self
    {
        $this->gmailUsers->attach($user);

        return $this;
    }

    /**
     * @param GmailUserInterface $user
     *
     * @return GmailDomain
     */
    public function removeGmailUser(GmailUserInterface $user): self
    {
        $this->gmailUsers->detach($user);

        return $this;
    }

    /**
     * @return \SplObjectStorage
     */
    public function getGmailUsers(): \SplObjectStorage
    {
        return $this->gmailUsers;
    }

    /**
     * @param string $userId
     *
     * @return GmailUserInterface|null
     */
    public function findGmailUserById(string $userId)
    {
        foreach ($this->gmailUsers as $user) {
            /** @var GmailUserInterface $user */
            if ($user->getUserId() === $userId) {
                return $user;
            }
        }

        return;
    }

    /**
     * @param string $primaryEmailAddress
     *
     * @return GmailUserInterface|null
     */
    public function findGmailUserByPrimaryEmail(string $primaryEmailAddress)
    {
        /** @var GmailUserInterface $user */
        foreach ($this->gmailUsers as $user) {
            if ($user->getPrimaryEmailAddress() === $primaryEmailAddress) {
                return $user;
            }
        }

        return;
    }

    /**
     * @param string $emailAliasAddress
     *
     * @return GmailUserInterface|null
     */
    public function findGmailUserByEmailAlias(string $emailAliasAddress)
    {
        /** @var GmailUserInterface $user */
        foreach ($this->gmailUsers as $user) {
            foreach ($user->getEmailAliases() as $emailAlias) {
                if ($emailAlias === $emailAliasAddress) {
                    return $user;
                }
            }
        }

        return;
    }

    /**
     * @param string $primaryOrAliasAddress
     *
     * @return GmailUserInterface|null
     */
    public function findGmailUserByEmail(string $primaryOrAliasAddress)
    {
        /** @var GmailUserInterface $user */
        foreach ($this->gmailUsers as $user) {
            foreach ($user->getAllEmailAddresses() as $emailAddress) {
                if ($emailAddress === $primaryOrAliasAddress) {
                    return $user;
                }
            }
        }

        return;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }
}
