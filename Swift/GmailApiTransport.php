<?php

namespace FL\GmailBundle\Swift;

use FL\GmailBundle\Services\Email;

/**
 * Class GmailApiTransport
 * @package FL\GmailBundle\Swift
 * @see \Swift_Transport_NullTransport for sample
 */
class GmailApiTransport implements \Swift_Transport
{

    /**
     * The event dispatcher from the plugin API
     */
    private $_eventDispatcher;

    /**
     * @var Email
     */
    private $email;

    /**
     * GmailApiTransport constructor.
     * @param Email $email
     * @param \Swift_Events_EventDispatcher $eventDispatcher
     */
    public function __construct(Email $email, \Swift_Events_EventDispatcher $eventDispatcher)
    {
        $this->email = $email;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function isStarted()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function start()
    {
    }

    /**
     * @inheritdoc
     */
    public function stop()
    {
    }

    /**
     * @inheritdoc
     */
    public function send(\Swift_Mime_Message $swiftMessage, &$failedRecipients = null)
    {
        $gmailMessage = new \Google_Service_Gmail_Message();
        $gmailMessage->setRaw($this->base64url_encode($swiftMessage));
        $fromArray = $swiftMessage->getFrom();

        // when sending, we can use email addresses, instead of a $userId
        // but we will only use the first email address, so everyone else will be a failed recipient
        $fromAddress = $this->fromArray_ToString($fromArray);
        $failedRecipients = $this->fromArray_ToFailedReceipients($fromArray);

        if ($evt = $this->_eventDispatcher->createSendEvent($this, $swiftMessage)) {
            $evt->setResult(0);
            $evt->setFailedRecipients($failedRecipients);
            $this->_eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
            if ($evt->bubbleCancelled()) {
                return 0;
            }
        }

        $this->email->send($fromAddress, $gmailMessage);
        $evt->setResult(1);
        $evt->fromEmailAddress = $fromAddress; // alternative to dispatching another event where we set fromEmailAddress

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
     * @inheritdoc
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
     * @return string
     */
    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * @param array $fromArray
     * @return string
     * The Gmail API can only take one address at a time, let's get the very first one
     */
    private function fromArray_ToString(array $fromArray): string
    {
        // $fromArray = [ 'john.doe@example.com' => 'John Doe', 'jane.doe@example.com' => 'Jane Doe' ]; // sample
        if (count($fromArray) > 0) {
            reset($fromArray);
            $fromString = key($fromArray);
            return $fromString;
        }
        throw new \InvalidArgumentException('Set at least one \'from\' address when using ' . self::class);
    }

    /**
     * @param array $fromArray
     * @return array
     * Also @see GmailApiTransport::fromArray_ToString()
     */
    private function fromArray_ToFailedReceipients(array $fromArray): array
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
