<?php

namespace FL\GmailBundle\Model;

/**
 * Class GmailMessage
 * Contains the relevant fields of a Gmail email.
 *
 * @link https://developers.google.com/gmail/api/v1/reference/users/messages#resource
 * @see Google_Service_Gmail_Message
 */
class GmailMessage implements GmailMessageInterface
{
    /**
     * Gmail ID for the email.
     *
     * @var string
     */
    protected $gmailId;

    /**
     * Gmail thread ID for the email.
     *
     * @var string
     */
    protected $threadId;

    /**
     * Gmail history ID for the email.
     *
     * @var string
     */
    protected $historyId;

    /**
     * Gmail user ID for the email.
     *
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
    protected $toCanonical;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $fromCanonical;

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
     * @var string
     */
    protected $bodyPlainText;

    /**
     * @var string
     */
    protected $bodyHtml;

    /**
     * @var string
     */
    protected $domain = '';

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
     * {@inheritdoc}
     */
    public function setGmailId(string $gmailId): GmailMessageInterface
    {
        $this->gmailId = $gmailId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGmailId()
    {
        return $this->gmailId;
    }

    /**
     * {@inheritdoc}
     */
    public function setThreadId(string $threadId): GmailMessageInterface
    {
        $this->threadId = $threadId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * Will convert even somewhat broken strings to an array of emails. E.g.:
     * email@example.com Miles <miles@example.com>, Mila <mila@example.com, Charles charles@example.com,,,,, <Mick> mick@example.com.
     *
     * @param string $email
     *
     * @return array
     */
    private function sanitizeEmailString(string $email)
    {
        $emails = [];
        foreach (preg_split('/(,|<|>|,|\\s)/', $email) as $possibleEmail) {
            if (filter_var($possibleEmail, FILTER_VALIDATE_EMAIL)) {
                $emails[] = strtolower($possibleEmail);
            }
        }

        return implode(',', $emails);
    }

    /**
     * {@inheritdoc}
     */
    public function setTo(string $to): GmailMessageInterface
    {
        $this->to = $to;
        $this->toCanonical = $this->sanitizeEmailString($to);

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
    public function getToCanonical()
    {
        return $this->toCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function setFrom(string $from): GmailMessageInterface
    {
        $this->from = $from;
        $this->fromCanonical = $this->sanitizeEmailString($from);

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
    public function getFromCanonical()
    {
        return $this->fromCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getReplyAllRecipients()
    {
        return $this->fromCanonical && $this->toCanonical
            ? $this->fromCanonical.','.$this->toCanonical
            : null;
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
     * {@inheritdoc}
     */
    public function setBodyPlainText(string $bodyPlainText = null): GmailMessageInterface
    {
        $this->bodyPlainText = $bodyPlainText;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBodyPlainText()
    {
        return $this->bodyPlainText;
    }

    /**
     * {@inheritdoc}
     */
    public function setBodyHtml(string $bodyHtml = null): GmailMessageInterface
    {
        $this->bodyHtml = $bodyHtml;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBodyHtml()
    {
        return $this->bodyHtml;
    }

    /**
     * {@inheritdoc}
     */
    public function addLabel(GmailLabelInterface $label): GmailMessageInterface
    {
        $this->labels->attach($label);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelByName(string $name)
    {
        /** @var GmailLabelInterface $label */
        foreach ($this->labels as $label) {
            if ($label->getName() === $name) {
                return $label;
            }
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function removeLabel(GmailLabelInterface $label): GmailMessageInterface
    {
        $this->labels->detach($label);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearLabels(): GmailMessageInterface
    {
        $this->labels = new \SplObjectStorage();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasLabel(string $name): bool
    {
        return ($this->getLabelByName($name) instanceof GmailLabelInterface) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId(string $userId): GmailMessageInterface
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

    /**
     * {@inheritdoc}
     */
    public function setHistoryId(string $historyId): GmailMessageInterface
    {
        $this->historyId = $historyId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHistoryId()
    {
        return $this->historyId;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain(string $domain): GmailMessageInterface
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUnread(): bool
    {
        return $this->hasLabel(static::LABEL_UNREAD);
    }

    /**
     * @return bool
     */
    public function isInbox(): bool
    {
        return $this->hasLabel(static::LABEL_INBOX);
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->hasLabel(static::LABEL_SENT);
    }

    /**
     * @return bool
     */
    public function isTrash(): bool
    {
        return $this->hasLabel(static::LABEL_TRASH);
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromGmailApiMessage(\Google_Service_Gmail_Message $gmailApiMessage, array $labels, string $userId, string $domain): GmailMessageInterface
    {
        /** @var GmailMessageInterface $message */
        $message = new static();

        /** @var \Google_Service_Gmail_MessagePart $payload */
        $payload = $gmailApiMessage->getPayload();
        /** @var \Google_Service_Gmail_MessagePartHeader[] $headers */
        $headers = $payload->getHeaders();

        foreach ($headers as $header) {
            switch ($header->getName()) {
                case 'From':
                    $message->setFrom($header->getValue());
                    break;
                case 'To':
                    $message->setTo($header->getValue());
                    break;
                case 'Date':
                    $message->setSentAt(new \DateTime($header->getValue()));
                    break;
                case 'Subject':
                    $message->setSubject($header->getValue());
                    break;
            }
        }

        $message
            ->setUserId($userId)
            ->setGmailId($gmailApiMessage->getId())
            ->setThreadId($gmailApiMessage->getThreadId())
            ->setHistoryId($gmailApiMessage->getHistoryId())
            ->setSnippet($gmailApiMessage->getSnippet())
            ->setBodyHtmlFromApiMessage($gmailApiMessage)
            ->setBodyPlainTextFromApiMessage($gmailApiMessage)
            ->setDomain($domain)
        ;

        /** @var GmailLabelInterface $label */
        foreach ($labels as $label) {
            $message->addLabel($label);
        }

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function setBodyPlainTextFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage): GmailMessageInterface
    {
        $this->bodyPlainText = static::resolveBodyPlainTextFromApiMessage($gmailApiMessage);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setBodyHtmlFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage): GmailMessageInterface
    {
        $this->bodyHtml = static::resolveBodyHtmlFromApiMessage($gmailApiMessage);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function resolveBodyHtmlFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage)
    {
        /** @var \Google_Service_Gmail_MessagePart $payload */
        $payload = $gmailApiMessage->getPayload();

        if ($payload->getMimeType() === 'multipart/alternative') {
            /** @var \Google_Service_Gmail_MessagePart $part */
            foreach ($payload->getParts() as $part) {
                if ($part->getMimeType() === 'text/html') {
                    return static::bodyToText($part->getBody());
                }
            }
        }

        return static::bodyToText($payload->getBody());
    }

    /**
     * {@inheritdoc}
     */
    public static function resolveBodyPlainTextFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage)
    {
        /** @var \Google_Service_Gmail_MessagePart $payload */
        $payload = $gmailApiMessage->getPayload();

        /** @var \Google_Service_Gmail_MessagePart $part */
        foreach ($payload->getParts() as $part) {
            if ($part->getMimeType() === 'text/plain') {
                return static::bodyToText($part->getBody());
            }
        }

        return;
    }

    /**
     * @param \Google_Service_Gmail_MessagePartBody $body
     *
     * @return string
     */
    private static function bodyToText(\Google_Service_Gmail_MessagePartBody $body)
    {
        $sanitizedData = strtr($body->getData(), '-_', '+/');

        return base64_decode($sanitizedData);
    }
}
