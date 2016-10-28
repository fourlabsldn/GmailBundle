<?php

namespace FL\GmailBundle\Model;

/**
 * Interface GmailMessageInterface
 * @package FL\GmailBundle\Model
 */
interface GmailMessageInterface
{
    /**
     * Set email recipient
     * @param string $to
     * @return GmailMessageInterface
     */
    public function setTo(string $to): GmailMessageInterface;

    /**
     * Get email recipient
     * @return string|null
     */
    public function getTo();

    /**
     * Returns an array of valid email strings, instead of a string that might contain multiple emails, names, '<', and '>'
     * @return string[]
     */
    public function getToEmails();

    /**
     * Returns comma separated emails, instead of a string that might contain multiple emails, names, '<', and '>'
     * @return string|null
     */
    public function getToEmailsCSV();

    /**
     * Set email sender
     * @param string $from
     * @return GmailMessageInterface
     */
    public function setFrom(string $from): GmailMessageInterface;

    /**
     * Get email sender
     * @return string|null
     */
    public function getFrom();

    /**
     * Returns a single valid email, instead of a string that might contain a name, '<', and '>'
     * @return string|null
     */
    public function getFromEmail();

    /**
     * Returns an array that merges getTo and getFrom
     * @return string[]
     */
    public function getAll();

    /**
     * Returns an array of valid email strings, instead of a string that might contain multiple emails, names, '<', and '>'
     * @return string[]
     */
    public function getAllEmails();

    /**
     * Returns comma separated emails, instead of a string that might contain multiple emails, names, '<', and '>'
     * @return string|null
     */
    public function getAllEmailsCSV();

    /**
     * Set email sent datetime
     * @param \DateTimeInterface $sentAt
     * @return GmailMessageInterface
     */
    public function setSentAt(\DateTimeInterface  $sentAt): GmailMessageInterface;

    /**
     * Get email sent date
     * @return \Datetime|null
     */
    public function getSentAt();

    /**
     * Set email subject
     * @param string $subject
     * @return GmailMessageInterface
     */
    public function setSubject(string $subject): GmailMessageInterface;

    /**
     * Get email subject
     * @return string|null
     */
    public function getSubject();

    /**
     * Set email snippet in plain text.
     * @param string $snippet
     * @return GmailMessageInterface
     */
    public function setSnippet(string $snippet): GmailMessageInterface;

    /**
     * Get email snippet in plain text.
     * @return string|null
     */
    public function getSnippet();

    /**
     * Set email body in plain text.
     * @param string|null $bodyPlainText
     * @return GmailMessageInterface
     */
    public function setBodyPlainText(string $bodyPlainText = null): GmailMessageInterface;

    /**
     * Get email body in plain text.
     * @return string|null
     */
    public function getBodyPlainText();

    /**
     * Set email body in HTML.
     * @param string|null $bodyHtml
     * @return GmailMessageInterface
     */
    public function setBodyHtml(string $bodyHtml = null): GmailMessageInterface;

    /**
     * Get email body in HTML.
     * @return string|null
     */
    public function getBodyHtml();

    /**
     * Add a label to the Gmail Message.
     * @param GmailLabelInterface $label
     * @return  GmailMessageInterface
     */
    public function addLabel(GmailLabelInterface $label): GmailMessageInterface;

    /**
     * Return the labels for this message.
     * @return array|\SplObjectStorage or other collection holders
     */
    public function getLabels();

    /**
     * Removes a label element
     *
     * @param GmailLabelInterface $label
     * @return GmailMessageInterface
     */
    public function removeLabel(GmailLabelInterface $label): GmailMessageInterface;

    /**
     * @return GmailMessageInterface
     */
    public function clearLabels(): GmailMessageInterface;

    /**
     * @param string $name
     * @return bool
     */
    public function hasLabel(string $name): bool;

    /**
     * Set the Gmail ID for this email
     * @param string $gmailId
     * @return GmailMessageInterface
     */
    public function setGmailId(string $gmailId): GmailMessageInterface;

    /**
     * Get the Gmail ID for this email
     * @return string|null
     */
    public function getGmailId();

    /**
     * Set the Gmail Thread ID for this email
     * @param string $threadId
     * @return GmailMessageInterface
     */
    public function setThreadId(string $threadId): GmailMessageInterface;

    /**
     * Get the Gmail Thread ID for this email
     * @return string|null
     */
    public function getThreadId();

    /**
     * Set the Gmail History ID for this email
     * @param string $historyId
     * @return GmailMessageInterface
     */
    public function setHistoryId(string $historyId): GmailMessageInterface;

    /**
     * Get the Gmail History ID for this email
     * @return string|null
     */
    public function getHistoryId();

    /**
     * Set the Gmail User ID for this email
     * @param string $userId
     * @return GmailMessageInterface
     */
    public function setUserId(string $userId): GmailMessageInterface;

    /**
     * Get the Gmail User ID for this email
     * @return string|null
     */
    public function getUserId();

    /**
     * @return string
     */
    public function getDomain(): string;

    /**
     * @param string $domain
     * @return GmailMessageInterface
     */
    public function setDomain(string $domain): GmailMessageInterface;

    /**
     * @return bool
     */
    public function isUnread(): bool;

    /**
     * @return bool
     */
    public function isInbox(): bool;

    /**
     * @return bool
     */
    public function isSent(): bool;

    /**
     * @return bool
     */
    public function isTrash(): bool;

    /**
     * Returns a new GmailMessageInterface instance initiated from the passed Google_Service_Gmail_Message instance.
     * The userId is done separately, because we cannot get it from \Google_Service_Gmail_Message
     * @param \Google_Service_Gmail_Message $gmailApiMessage
     * @param GmailLabelInterface[] $labels
     * @param string $userId
     * @param string $domain
     * @return GmailMessageInterface
     *
     * NOTE:
     *
     * You can store GmailMessages without their body,
     * and then set them at runtime with these methods:
     *
     * @see GmailMessageInterface::setBodyHtmlFromApiMessage()
     * @see GmailMessageInterface::setBodyPlainTextFromApiMessage()
     * @see GmailMessageInterface::resolveBodyHtmlFromApiMessage()
     * @see GmailMessageInterface::resolveBodyPlainTextFromApiMessage()
     */
    public static function createFromGmailApiMessage(\Google_Service_Gmail_Message $gmailApiMessage, array $labels, string $userId, string $domain): GmailMessageInterface;

    /**
     * @param \Google_Service_Gmail_Message $gmailApiMessage
     * @return GmailMessageInterface
     */
    public function setBodyHtmlFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage): GmailMessageInterface;

    /**
     * @param \Google_Service_Gmail_Message $gmailApiMessage
     * @return GmailMessageInterface
     */
    public function setBodyPlainTextFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage): GmailMessageInterface;

    /**
     * @param \Google_Service_Gmail_Message $gmailApiMessage
     * @return null|string
     */
    public static function resolveBodyHtmlFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage);

    /**
     * @param \Google_Service_Gmail_Message $gmailApiMessage
     * @return null|string
     */
    public static function resolveBodyPlainTextFromApiMessage(\Google_Service_Gmail_Message $gmailApiMessage);
}
