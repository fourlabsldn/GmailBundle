<?php

namespace FL\GmailBundle\Model;

/**
 * Class GmailMessage
 * Contains the relevant fields of a Gmail email.
 * @link https://developers.google.com/gmail/api/v1/reference/users/messages#resource
 * @see Google_Service_Gmail_Message
 * @package FL\GmailBundle\Model
 */
class GmailMessage implements GmailMessageInterface
{
    /**
     * Gmail ID for the email.
     * @var string
     */
    protected $gmailId;

    /**
     * Gmail thread ID for the email.
     * @var string
     */
    protected $threadId;

    /**
     * Gmail history ID for the email.
     * @var string
     */
    protected $historyId;

    /**
     * Gmail user ID for the email.
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $to;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var \DateTimeInterface
     */
    protected $sentAt;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $snippet;

    /**
     * @var \SplObjectStorage
     */
    protected $labels;

    /**
     * GmailMessage constructor.
     */
    public function __construct()
    {
        $this->labels = new \SplObjectStorage();
    }

    /**
     * @inheritdoc
     */
    public function setGmailId(string $gmailId): GmailMessageInterface
    {
        $this->gmailId = $gmailId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGmailId()
    {
        return $this->gmailId;
    }

    /**
     * @inheritdoc
     */
    public function setThreadId(string $threadId): GmailMessageInterface
    {
        $this->threadId = $threadId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTo(string $to): GmailMessageInterface
    {
        $this->to = $to;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * {@inheritdoc}
     */
    public function setFrom(string $from): GmailMessageInterface
    {
        $this->from = $from;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * {@inheritdoc}
     */
    public function setSentAt(\DateTimeInterface $sentAt): GmailMessageInterface
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject(string $subject): GmailMessageInterface
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function setSnippet(string $snippet): GmailMessageInterface
    {
        $this->snippet = $snippet;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSnippet()
    {
        return $this->snippet;
    }

    /**
     * @inheritdoc
     */
    public function addLabel(GmailLabelInterface $label): GmailMessageInterface
    {
        $this->labels->attach($label);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @inheritdoc
     */
    public function removeLabel(GmailLabelInterface $label): GmailMessageInterface
    {
        $this->labels->detach($label);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setUserId(string $userId): GmailMessageInterface
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @inheritdoc
     */
    public function setHistoryId(string $historyId): GmailMessageInterface
    {
        $this->historyId = $historyId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHistoryId()
    {
        return $this->historyId;
    }

    /**
     * @inheritdoc
     */
    final public static function createFromGmailApiMessage(\Google_Service_Gmail_Message $gmailApiMessage, array $labels, string $userId): GmailMessageInterface
    {
        /** @var GmailMessageInterface $message */
        $message =  new static();

        /** @var \Google_Service_Gmail_MessagePartHeader[] $headers */
        $headers = $gmailApiMessage->getPayload()->getHeaders();

        foreach ($headers as $header) {
            switch ($header->getName()) {
                case "From":
                    $message->setFrom($header->getValue());
                    break;
                case "To":
                    $message->setTo($header->getValue());
                    break;
                case "Date":
                    $message->setSentAt(new \DateTime($header->getValue()));
                    break;
                case "Subject":
                    $message->setSubject($header->getValue());
                    break;
            }
        }

        $message->setUserId($userId)
            ->setGmailId($gmailApiMessage->getId())
            ->setThreadId($gmailApiMessage->getThreadId())
            ->setHistoryId($gmailApiMessage->getHistoryId())
            ->setSnippet($gmailApiMessage->getSnippet());
        ;

        /** @var GmailLabelInterface $label */
        foreach ($labels as $label) {
            $message->addLabel($label);
        }

        return $message;
    }
}
