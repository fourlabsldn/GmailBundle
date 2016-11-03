<?php

namespace FL\GmailBundle\Model;

/**
 * Class GmailUserInterface.
 */
class GmailUser implements GmailUserInterface
{
    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $primaryEmailAddress;

    /**
     * @var string[]
     */
    protected $emailAliases;

    /**
     * GmailUser constructor.
     */
    public function __construct()
    {
        $this->emailAliases = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId() : string
    {
        return $this->userId;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId(string $userId) : GmailUserInterface
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryEmailAddress() : string
    {
        return $this->primaryEmailAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrimaryEmailAddress(string $primaryEmailAddress) : GmailUserInterface
    {
        $this->primaryEmailAddress = $primaryEmailAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailAliases() : array
    {
        return array_values($this->emailAliases);
    }

    /**
     * {@inheritdoc}
     */
    public function addEmailAlias(string $emailAlias) : GmailUserInterface
    {
        $this->emailAliases[$emailAlias] = $emailAlias;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeEmailAlias(string $emailAlias) : GmailUserInterface
    {
        unset($this->emailAliases[$emailAlias]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllEmailAddresses() : array
    {
        $emails = $this->getEmailAliases();
        $emails[] = $this->primaryEmailAddress;

        return $emails;
    }
}
