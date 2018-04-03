<?php

namespace FL\GmailBundle\Model\Collection;

use FL\GmailBundle\Model\GmailMessageInterface;

/**
 * Abstraction of a collection of GmailMessages.
 *
 * This class is not meant to be persisted.
 */
class GmailMessageCollection
{
    /**
     * @var \SplObjectStorage
     */
    private $messages;

    /**
     * MessageCollection constructor.
     */
    public function __construct()
    {
        $this->messages = new \SplObjectStorage();
    }

    /**
     * @param GmailMessageInterface $message
     *
     * @return GmailMessageCollection
     */
    public function addMessage(GmailMessageInterface $message): self
    {
        $this->messages->attach($message);

        return $this;
    }

    /**
     * @param GmailMessageInterface $message
     *
     * @return GmailMessageCollection
     */
    public function removeMessage(GmailMessageInterface $message): self
    {
        $this->messages->detach($message);

        return $this;
    }

    /**
     * @return \SplObjectStorage
     */
    public function getMessages(): \SplObjectStorage
    {
        return $this->messages;
    }

    /**
     * @param GmailMessageInterface $message
     *
     * @return bool
     */
    public function hasMessage(GmailMessageInterface $message): bool
    {
        if ($this->messages->contains($message)) {
            return true;
        }

        return false;
    }
}
