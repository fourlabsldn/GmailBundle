<?php

namespace FL\GmailBundle\Model;

class GmailMessage implements GmailMessageInterface
{
    /**
     * @var string
     */
    protected $gmailId;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $threadId;

    /**
     * @var string
     */
    protected $historyId;

    /**
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
     * @var \SplObjectStorage
     */
    protected $labels;

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
    public function getGmailId(): string
    {
        return $this->gmailId;
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
    public function getThreadId(): string
    {
        return $this->threadId;
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
    public function getHistoryId(): string
    {
        return $this->historyId;
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
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTo(string $to): GmailMessageInterface
    {
        $this->to = $to;
        $this->toCanonical = $this->canonicalizeEmailString($to);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * {@inheritdoc}
     */
    public function getToCanonical(): string
    {
        return $this->toCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function setFrom(string $from): GmailMessageInterface
    {
        $this->from = $from;
        $this->fromCanonical = $this->canonicalizeEmailString($from);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromCanonical(): string
    {
        return $this->fromCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getReplyAllRecipients(): string
    {
        return $this->fromCanonical.','.$this->toCanonical;
    }

    /**
     * Will convert even somewhat broken strings to a string of comma separated emails
     * email@example.com Miles <miles@example.com>, Mila <mila@example.com, Charles charles@example.com,,,,, <Mick> mick@example.com.
     * email@example.com, miles@example.com, mila@example.com, charles@example.com, mick@example.com.
     *
     * @param string $email
     *
     * @return array
     */
    private function canonicalizeEmailString(string $email)
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
    public function setSentAt(\DateTimeInterface $sentAt): GmailMessageInterface
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSentAt(): \DateTimeInterface
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
    public function getSubject(): string
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
    public function getSnippet(): string
    {
        return $this->snippet;
    }

    /**
     * {@inheritdoc}
     */
    public function setBodyPlainTextFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage): GmailMessageInterface
    {
        $this->bodyPlainText = static::resolveBodyPlainTextFromApiMessage($gmailApiMessage) ?? '';

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBodyPlainText(): string
    {
        return $this->bodyPlainText;
    }

    /**
     * {@inheritdoc}
     */
    public function setBodyHtmlFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage): GmailMessageInterface
    {
        $this->bodyHtml = static::resolveBodyHtmlFromApiMessage($gmailApiMessage) ?? '';

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBodyHtml(): string
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
    public function getLabels(): \Traversable
    {
        return $this->labels;
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
    public function hasLabelByName(string $name): bool
    {
        return ($this->getLabelByName($name) instanceof GmailLabelInterface) ? true : false;
    }

    /**
     * @return bool
     */
    public function isUnread(): bool
    {
        return $this->hasLabelByName(static::LABEL_UNREAD);
    }

    /**
     * @return bool
     */
    public function isInbox(): bool
    {
        return $this->hasLabelByName(static::LABEL_INBOX);
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->hasLabelByName(static::LABEL_SENT);
    }

    /**
     * @return bool
     */
    public function isTrash(): bool
    {
        return $this->hasLabelByName(static::LABEL_TRASH);
    }

    /**
     * @param \Google_Service_Gmail_Message
     *
     * @return string|null
     */
    private static function resolveBodyHtmlFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage)
    {
        /** @var \Google_Service_Gmail_MessagePart $payload */
        $payload = $gmailApiMessage->getPayload();

        if ($partsBody = static::getBodyFromParts($payload->getParts(), 'text/html')) {
            return $partsBody;
        }

        if ($payload->getBody() instanceof \Google_Service_Gmail_MessagePartBody) {
            return static::bodyToText($payload->getBody());
        }

        return;
    }

    /**
     * @param \Google_Service_Gmail_Message
     *
     * @return string|null
     */
    private static function resolveBodyPlainTextFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage)
    {
        /** @var \Google_Service_Gmail_MessagePart $payload */
        $payload = $gmailApiMessage->getPayload();

        if ($partsBody = static::getBodyFromParts($payload->getParts(), 'text/plain')) {
            return $partsBody;
        }

        if ($payload->getBody() instanceof \Google_Service_Gmail_MessagePartBody) {
            return static::bodyToText($payload->getBody());
        }

        return;
    }

    /**
     * @param array  $parts
     * @param string $bodyMimeType (E.g. 'text/plain', 'text/html')
     *
     * @return string|null
     */
    private static function getBodyFromParts(array $parts, string $bodyMimeType)
    {
        foreach ($parts as $part) {
            if ($body = static::getBodyFromParts($part->getParts(), $bodyMimeType)) {
                return $body;
            }
            if (
                $part->getMimeType() === $bodyMimeType &&
                $part->getBody() instanceof \Google_Service_Gmail_MessagePartBody &&
                $body = static::bodyToText($part->getBody())
            ) {
                return $body;
            }
        }

        return;
    }

    /**
     * @param \Google_Service_Gmail_MessagePartBody|null $body
     *
     * @return string
     */
    private static function bodyToText(\Google_Service_Gmail_MessagePartBody $body = null): string
    {
        $sanitizedData = strtr($body->getData(), '-_', '+/');

        // On fail base64_decode returns false.
        // We can't know with certainty that getData() is ok.
        // So fail gracefully.
        return base64_decode($sanitizedData) ?? '';
    }
}
