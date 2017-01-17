<?php

namespace FL\GmailBundle\Factory;

use FL\GmailBundle\Model\GmailLabelInterface;

class GmailLabelFactory
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

        $gmailLabelObject = new $gmailLabelClass();
        if (!$gmailLabelObject instanceof GmailLabelInterface) {
            throw new \InvalidArgumentException();
        }

        $this->gmailLabelClass = $gmailLabelClass;
    }

    /**
     * Transform $labelName, $userId, $domain into a Label.
     * The Label class is defined by $this->labelClass.
     *
     * Returns a new GmailLabelInterface instance from the passed arguments.
     * We don't create from an email \Google_Service_Gmail_Message, because we need to fetch label names.
     * We don't create from a labels \Google_Service_Gmail_Message, because we need to cache labels
     * such that they are not fetched twice.
     *
     * @param string $name
     * @param string $domain
     * @param string $userId
     *
     * @return GmailLabelInterface
     */
    public function createFromProperties(string $name, string $domain, string $userId): GmailLabelInterface
    {
        /** @var GmailLabelInterface $label */
        $label = new $this->gmailLabelClass();
        $label
            ->setName($name)
            ->setDomain($domain)
            ->setUserId($userId);

        return $label;
    }
}
