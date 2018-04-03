<?php

namespace FL\GmailBundle\Model;

/**
 * Concrete classes help you persist GmailLabels,
 * for a userId, in a domain.
 *
 * @see https://developers.google.com/gmail/api/guides/labels
 */
interface GmailLabelInterface
{
    /**
     * @param string $userId
     *
     * @return GmailLabelInterface
     */
    public function setUserId(string $userId): self;

    /**
     * @return string
     */
    public function getUserId(): string;

    /**
     * @param string $domain
     *
     * @return GmailLabelInterface
     */
    public function setDomain(string $domain): self;

    /**
     * @return string
     */
    public function getDomain(): string;

    /**
     * @param string $name
     *
     * @return GmailLabelInterface
     */
    public function setName(string $name): self;

    /**
     * @return string
     */
    public function getName(): string;
}
