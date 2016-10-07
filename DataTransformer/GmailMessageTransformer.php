<?php

namespace FL\GmailBundle\DataTransformer;

use FL\GmailBundle\Model\GmailMessageInterface;
use FL\GmailBundle\Model\GmailLabelInterface;

/**
 * Class GmailMessageTransformer
 * We are unable to provide a function for reverseTransform() due to the
 * fact that we're only saving the attributes of interest from
 * \Google_Service_Gmail_Message.
 * @package FL\GmailBundle\DataTransformer
 */
class GmailMessageTransformer
{
    /**
     * @var string
     */
    private $gmailMessageClass;

    /**
     * GmailMessageTransformer constructor.
     * @param string $gmailMessageClass
     */
    public function __construct(string $gmailMessageClass)
    {
        if (!class_exists($gmailMessageClass)) {
            throw new \InvalidArgumentException();
        }

        $gmailMessageObject = new $gmailMessageClass;
        if (!$gmailMessageObject instanceof GmailMessageInterface) {
            throw new \InvalidArgumentException();
        }

        $this->gmailMessageClass = $gmailMessageClass;
    }

    /**
     * Transform a \Google_Service_Gmail_Message into a GmailMessage.
     * The GmailMessage class is defined by $this->gmailClass.
     * @param \Google_Service_Gmail_Message $message
     * @param GmailLabelInterface[] $labels
     * @param string $userId
     * @return GmailMessageInterface
     */
    public function transform(\Google_Service_Gmail_Message $message, array $labels, string $userId): GmailMessageInterface
    {
        /** @var GmailMessageInterface $gmailMessageClass (not really an instance of an object, but helps auto-complete) */
        $gmailMessageClass = $this->gmailMessageClass;
        $gmailMessage = $gmailMessageClass::createFromGmailApiMessage($message, $labels, $userId);

        return $gmailMessage;
    }
}
