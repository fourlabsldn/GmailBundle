<?php

namespace FL\GmailBundle\Factory;

use FL\GmailBundle\Model\GmailMessageInterface;
use FL\GmailBundle\Model\GmailLabelInterface;

class GmailMessageFactory
{
    /**
     * @var string
     */
    private $gmailMessageClass;

    /**
     * @param string $gmailMessageClass
     */
    public function __construct(string $gmailMessageClass)
    {
        if (!class_exists($gmailMessageClass)) {
            throw new \InvalidArgumentException();
        }

        $gmailMessageObject = new $gmailMessageClass();
        if (!$gmailMessageObject instanceof GmailMessageInterface) {
            throw new \InvalidArgumentException();
        }

        $this->gmailMessageClass = $gmailMessageClass;
    }

    /**
     * Transform a \Google_Service_Gmail_Message into a GmailMessage.
     * The GmailMessage class is defined by $this->gmailClass.
     *
     * Returns a new GmailMessageInterface instance from the passed Google_Service_Gmail_Message.
     * The userId is done separately, because we cannot get it from \Google_Service_Gmail_Message.
     * The labels are done separately, because \Google_Service_Gmail_Message only has labelIds.
     * The domain is done separately, because we cannot get it from \Google_Service_Gmail_Message.
     *
     * @param \Google_Service_Gmail_Message $gmailApiMessage
     * @param GmailLabelInterface[]         $labels
     * @param string                        $userId
     * @param string                        $domain
     *
     * @return GmailMessageInterface
     */
    public function createFromGmailApiMessage(\Google_Service_Gmail_Message $gmailApiMessage, array $labels, string $userId, string $domain): GmailMessageInterface
    {
        /** @var GmailMessageInterface $message */
        $message = new $this->gmailMessageClass();

        /** @var \Google_Service_Gmail_MessagePart $payload */
        $payload = $gmailApiMessage->getPayload();
        /** @var \Google_Service_Gmail_MessagePartHeader[] $headers */
        $headers = $payload->getHeaders();

        $headerNames = [];
        foreach ($headers as $header) {
            switch ($header->getName()) {
                case 'From':
                    $message->setFrom($header->getValue());
                    break;
                case 'To':
                    $message->setTo($header->getValue());
                    break;
                case 'Date':
                    // Google Dates come with an extra timezone.
                    // E.g. "Tue, 24 Jan 2017 12:00:02 +0000 (GMT Standard Time)" "Tue, 24 Jan 2017 12:00:02 +0000"
                    $dateString = preg_replace('/\(.+\)/', '', $header->getValue());
                    $date = new \DateTime($dateString);
                    //convert the date to the system's default timezone
                    $date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
                    $message->setSentAt($date);
                    break;
                case 'Subject':
                    $message->setSubject($header->getValue() ?? '');
                    break;
            }
            $headerNames[] = $header->getName();
        }

        // If From / To Headers aren't there, fail gracefully
        if (!in_array('From', $headerNames)) {
            $message->setFrom('');
        }
        if (!in_array('To', $headerNames)) {
            $message->setTo('');
        }
        if (!in_array('Date', $headerNames)) {
            $message->setSentAt(new \DateTime());
        }
        if (!in_array('Subject', $headerNames)) {
            $message->setSubject('');
        }

        $message
            ->setUserId($userId)
            ->setGmailId($gmailApiMessage->getId())
            ->setThreadId($gmailApiMessage->getThreadId())
            ->setHistoryId($gmailApiMessage->getHistoryId())
            ->setSnippet($gmailApiMessage->getSnippet() ?? '')
            ->setBodyHtmlFromApiMessage($gmailApiMessage)
            ->setBodyPlainTextFromApiMessage($gmailApiMessage)
            ->setDomain($domain);

        /** @var GmailLabelInterface $label */
        foreach ($labels as $label) {
            $message->addLabel($label);
        }

        return $message;
    }
}
