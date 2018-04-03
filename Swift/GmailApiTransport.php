<?php

namespace FL\GmailBundle\Swift;

use FL\GmailBundle\Services\Email;

/**
 * Provides a SwiftMailer transport that enables
 * using the Gmail API to send email.
 *
 * @see \Swift_Transport_NullTransport for sample
 */
class GmailApiTransport implements \Swift_Transport
{
    /**
     * The event dispatcher from the plugin API.
     */
    private $_eventDispatcher;

    /**
     * @var Email
     */
    private $email;

    /**
     * @param Email                         $email
     * @param \Swift_Events_EventDispatcher $eventDispatcher
     */
    public function __construct(Email $email, \Swift_Events_EventDispatcher $eventDispatcher)
    {
        $this->email = $email;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function ping()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function send(\Swift_Mime_SimpleMessage $swiftMessage, &$failedRecipients = null)
    {
        $gmailMessage = new \Google_Service_Gmail_Message();
        $gmailMessage->setRaw($this->base64UrlEncode($swiftMessage));
        if ($swiftMessage instanceof SwiftGmailMessage) {
            $gmailMessage->setThreadId($swiftMessage->getThreadId());
        }
        $fromArray = $swiftMessage->getFrom();

        // When sending, Google allows us to use $userIds. But we will use email addresses.
        // And we will only use the first email address, so everyone else will be a failed recipient
        $fromAddress = $this->fromArrayToString($fromArray);
        $failedRecipients = $this->fromArrayToFailedRecipients($fromArray);

        if ($evt = $this->_eventDispatcher->createSendEvent($this, $swiftMessage)) {
            $evt->setResult(0);
            $evt->setFailedRecipients($failedRecipients);
            $this->_eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
            if ($evt->bubbleCancelled()) {
                return 0;
            }
        }

        $this->email->sendFromEmail($fromAddress, $gmailMessage);
        $evt->setResult(1);
        // When messages are sent, we might want to re-sync with the Google API.
        // We will rely on SwiftMailer events to do this.
        // The caveat is that the event cannot transmit the fromEmailAddress.
        // So we'll have to do it with this hack.
        $evt->fromEmailAddress = $fromAddress;

        if ($evt) {
            $evt->setResult(\Swift_Events_SendEvent::RESULT_SUCCESS);
            $this->_eventDispatcher->dispatchEvent($evt, 'sendPerformed');
        }

        $count = (
            count((array) $swiftMessage->getTo())
            + count((array) $swiftMessage->getCc())
            + count((array) $swiftMessage->getBcc())
        );

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function registerPlugin(\Swift_Events_EventListener $plugin)
    {
        $this->_eventDispatcher->bindEventListener($plugin);
    }

    /**
     * Proper encoding for Google Emails.
     *
     * The Gmail API requires MIME email messages compliant with RFC 2822
     * and encoded as base64url strings according to the official docs.
     * https://developers.google.com/gmail/api/guides/sending
     *
     * In order to comply PHP needs an additional step than just base64_encode.
     * Function obtained from: http://stackoverflow.com/questions/29893570
     *
     * @param $data
     *
     * @return string
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * The Gmail API can only take one address at a time, let's get the very first one.
     *
     * @param array $fromArray
     *
     * @return string
     */
    private function fromArrayToString(array $fromArray): string
    {
        // $fromArray = [ 'john.doe@example.com' => 'John Doe', 'jane.doe@example.com' => 'Jane Doe' ]; // sample
        if (count($fromArray) > 0) {
            reset($fromArray);
            $fromString = key($fromArray);

            return $fromString;
        }

        throw new \InvalidArgumentException('Set at least one \'from\' address when using '.self::class);
    }

    /**
     * The Gmail API can only take one address at a time, the others are failed recipients.
     *
     * @param array $fromArray
     *
     * @return array
     */
    private function fromArrayToFailedRecipients(array $fromArray): array
    {
        if (count($fromArray) > 0) {
            reset($fromArray);
            $keyOfFirstElement = key($fromArray); // john_doe@example.com
            unset($fromArray[$keyOfFirstElement]);

            return $fromArray;
        }

        return [];
    }
}
