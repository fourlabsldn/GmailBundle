<?php

namespace FL\GmailBundle\Model;

/**
 * Concrete classes help you persist GmailMessages,
 * for a userId, in a domain.
 *
 * @see https://developers.google.com/gmail/api/v1/reference/users/messages
 */
interface GmailMessageInterface
{
    /**
     * These labels are special because they always exist in Gmail.
     *
     * @see https://developers.google.com/gmail/api/guides/labels
     */
    const LABEL_UNREAD = 'UNREAD';

    const LABEL_INBOX = 'INBOX';

    const LABEL_SENT = 'SENT';

    const LABEL_TRASH = 'TRASH';

    /**
     * @param string $gmailId
     *
     * @return GmailMessageInterface
     */
    public function setGmailId(string $gmailId): self;

    /**
     * @return string
     */
    public function getGmailId(): string;

    /**
     * @param string $domain
     *
     * @return GmailMessageInterface
     */
    public function setDomain(string $domain): self;

    /**
     * @return string
     */
    public function getDomain(): string;

    /**
     * @param string $threadId
     *
     * @return GmailMessageInterface
     */
    public function setThreadId(string $threadId): self;

    /**
     * @return string
     */
    public function getThreadId(): string;

    /**
     * @param string $historyId
     *
     * @return GmailMessageInterface
     */
    public function setHistoryId(string $historyId): self;

    /**
     * @return string
     */
    public function getHistoryId(): string;

    /**
     * @param string $userId
     *
     * @return GmailMessageInterface
     */
    public function setUserId(string $userId): self;

    /**
     * @return string
     */
    public function getUserId(): string;

    /**
     * Recipients string in email format.
     * E.g. Jane Doe <jane.doe@example.com>, John Doe <john.doe@example.com>.
     *
     * @param string $to
     *
     * @return GmailMessageInterface
     */
    public function setTo(string $to): self;

    /**
     * @return string
     */
    public function getTo(): string;

    /**
     * Returns the recipients as a canonicalized string.
     * A comma separated list of emails.
     *
     * @return string
     */
    public function getToCanonical(): string;

    /**
     * Sender string in email format.
     * E.g. Jane Doe <jane.doe@example.com>.
     *
     * @param string $from
     *
     * @return GmailMessageInterface
     */
    public function setFrom(string $from): self;

    /**
     * @return string
     */
    public function getFrom(): string;

    /**
     * Returns the sender's canonicalized email address.
     *
     * @return string
     */
    public function getFromCanonical(): string;

    /**
     * Returns a combined comma separated list,
     * of getToCanonical and getFromCanonical.
     *
     * @return string
     */
    public function getReplyAllRecipients(): string;

    /**
     * @param \DateTimeInterface $sentAt
     *
     * @return GmailMessageInterface
     */
    public function setSentAt(\DateTimeInterface  $sentAt): self;

    /**
     * @return \DateTimeInterface
     */
    public function getSentAt(): \DateTimeInterface;

    /**
     * @param string $subject
     *
     * @return GmailMessageInterface
     */
    public function setSubject(string $subject): self;

    /**
     * @return string
     */
    public function getSubject(): string;

    /**
     * @param string $snippet
     *
     * @return GmailMessageInterface
     */
    public function setSnippet(string $snippet): self;

    /**
     * @return string
     */
    public function getSnippet(): string;

    /**
     * Set from a \Google_Service_Gmail_Message (from the API).
     *
     * This method is used the first time a message is resolved.
     * If a GmailMessage is persisted without a bodyPlainText,
     * this method can be used, to re-resolve the bodyPlainText.
     *
     * @param \Google_Service_Gmail_Message $gmailApiMessage
     *
     * @return GmailMessageInterface
     */
    public function setBodyPlainTextFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage): self;

    /**
     * @return string
     */
    public function getBodyPlainText(): string;

    /**
     * @param string $bodyPlainText
     *
     * @return GmailMessageInterface
     */
    public function setBodyPlainText(string $bodyPlainText): self;

    /**
     * Set from a \Google_Service_Gmail_Message (from the API).
     *
     * This method is used the first time a message is resolved.
     * If a GmailMessage is persisted without a bodyHtml,
     * this method can be used, to re-resolve the bodyHtml.
     *
     * @param \Google_Service_Gmail_Message $gmailApiMessage
     *
     * @return GmailMessageInterface
     */
    public function setBodyHtmlFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage): self;

    /**
     * @param string $bodyHtml
     *
     * @return GmailMessageInterface
     */
    public function setBodyHtml(string $bodyHtml): self;

    /**
     * @return string
     */
    public function getBodyHtml(): string;

    /**
     * @param GmailLabelInterface $label
     *
     * @return GmailMessageInterface
     */
    public function addLabel(GmailLabelInterface $label): self;

    /**
     * @return \Traversable
     */
    public function getLabels(): \Traversable;

    /**
     * @param GmailLabelInterface $label
     *
     * @return GmailMessageInterface
     */
    public function removeLabel(GmailLabelInterface $label): self;

    /**
     * @return GmailMessageInterface
     */
    public function clearLabels(): self;

    /**
     * @param string $name
     *
     * @return GmailLabelInterface|null
     */
    public function getLabelByName(string $name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasLabelByName(string $name): bool;

    /**
     * Has the label @see GmailMessageInterface::LABEL_UNREAD.
     *
     * @return bool
     */
    public function isUnread(): bool;

    /**
     * Has the label @see GmailMessageInterface::LABEL_INBOX.
     *
     * @return bool
     */
    public function isInbox(): bool;

    /**
     * Has the label @see GmailMessageInterface::LABEL_SENT.
     *
     * @return bool
     */
    public function isSent(): bool;

    /**
     * Has the label @see GmailMessageInterface::LABEL_TRASH.
     *
     * @return bool
     */
    public function isTrash(): bool;
}
