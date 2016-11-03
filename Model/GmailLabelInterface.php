<?php

namespace FL\GmailBundle\Model;

/**
 * Interface GmailLabelInterface.
 */
interface GmailLabelInterface
{
    /**
     * Set the label name.
     *
     * @param string $name
     *
     * @return GmailLabelInterface
     */
    public function setName(string $name): GmailLabelInterface;

    /**
     * Get the label name.
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set the label's userId.
     *
     * @param string $userId
     *
     * @return GmailLabelInterface
     */
    public function setUserId(string $userId): GmailLabelInterface;

    /**
     * Get the label's userId.
     *
     * @return string|null
     */
    public function getUserId();
}
