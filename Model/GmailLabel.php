<?php

namespace FL\GmailBundle\Model;

/**
 * Class GmailLabel
 * Contains the labels applied to a Gmail Message.
 */
class GmailLabel implements GmailLabelInterface
{
    protected $name;

    protected $userId;

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): GmailLabelInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId(string $userId): GmailLabelInterface
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
}
