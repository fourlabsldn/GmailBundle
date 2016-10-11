<?php

namespace FL\GmailBundle\Swift;

class SwiftGmailMessage extends \Swift_Message
{
    /**
     * @var string
     */
    protected $threadId = null;

    /**
     * @return string
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * @param string|null $threadId
     * @return SwiftGmailMessage
     */
    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;

        return $this;
    }

    /**
     * Create a new Message.
     *
     * @param string $subject
     * @param string $body
     * @param string $contentType
     * @param string $charset
     *
     * @return SwiftGmailMessage
     */
    public static function newInstance($subject = null, $body = null, $contentType = null, $charset = null)
    {
        return new self($subject, $body, $contentType, $charset);
    }
}