<?php

namespace FL\GmailBundle\DataTransformer;

use FL\GmailBundle\Model\GmailLabelInterface;

/**
 * @package FL\GmailBundle\DataTransformer
 */
class GmailLabelTransformer
{
    /**
     * @var string
     */
    private $gmailLabelClass;

    /**
     * @param string $gmailLabelClass
     */
    public function __construct(string $gmailLabelClass)
    {
        if (!class_exists($gmailLabelClass)) {
            throw new \InvalidArgumentException();
        }

        $gmailLabelObject = new $gmailLabelClass;
        if (!$gmailLabelObject instanceof GmailLabelInterface) {
            throw new \InvalidArgumentException();
        }

        $this->gmailLabelClass = $gmailLabelClass;
    }

    /**
     * Transform a string $labelName into a Label.
     * The Label class is defined by $this->labelClass.
     * @param string $labelName
     * @param string $userId
     * @return GmailLabelInterface
     */
    public function transform(string $labelName, string $userId): GmailLabelInterface
    {
        /** @var GmailLabelInterface $label */
        $label = new $this->gmailLabelClass;
        $label->setName($labelName)
            ->setUserId($userId);

        return $label;
    }
}
