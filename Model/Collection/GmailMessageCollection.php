<?php

namespace FL\GmailBundle\Model\Collection;

use FL\GmailBundle\Model\GmailMessageInterface;

/**
 * Class MessageCollection
 * @package FL\GmailBundle\Model
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
     * @return GmailMessageCollection
     */
    public function addMessage(GmailMessageInterface $message): GmailMessageCollection
    {
        $this->messages->attach($message);

        return $this;
    }

    /**
     * @param GmailMessageInterface $message
     * @return GmailMessageCollection
     */
    public function removeMessage(GmailMessageInterface $message): GmailMessageCollection
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
