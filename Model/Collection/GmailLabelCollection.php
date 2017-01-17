<?php

namespace FL\GmailBundle\Model\Collection;

use FL\GmailBundle\Model\GmailLabelInterface;

/**
 * Abstraction of a collection of Labels
 * This is useful to search a collection of Labels by name or userId.
 *
 * This class is not meant to be persisted.
 */
class GmailLabelCollection
{
    /**
     * @var \SplObjectStorage
     */
    private $labels;

    /**
     * LabelCollection constructor.
     */
    public function __construct()
    {
        $this->labels = new \SplObjectStorage();
    }

    /**
     * @param GmailLabelInterface $label
     *
     * @return GmailLabelCollection
     */
    public function addLabel(GmailLabelInterface $label): GmailLabelCollection
    {
        $this->labels->attach($label);

        return $this;
    }

    /**
     * @param GmailLabelInterface $label
     *
     * @return GmailLabelCollection
     */
    public function removeLabel(GmailLabelInterface $label): GmailLabelCollection
    {
        $this->labels->detach($label);

        return $this;
    }

    /**
     * @return \SplObjectStorage
     */
    public function getLabels(): \SplObjectStorage
    {
        return $this->labels;
    }

    /**
     * @param GmailLabelInterface $label
     *
     * @return bool
     */
    public function hasLabel(GmailLabelInterface $label): bool
    {
        if ($this->labels->contains($label)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $labelName
     *
     * @return GmailLabelInterface|null
     */
    public function getLabelOfName(string $labelName)
    {
        foreach ($this->labels as $label) {
            /** @var GmailLabelInterface $label */
            if ($label->getName() === $labelName) {
                return $label;
            }
        }

        return;
    }

    /**
     * @param string $userId
     *
     * @return GmailLabelInterface|null
     */
    public function getLabelOfUserId(string $userId)
    {
        foreach ($this->labels as $label) {
            /** @var GmailLabelInterface $label */
            if ($label->getUserId() === $userId) {
                return $label;
            }
        }

        return;
    }

    /**
     * @param string $labelName
     * @param string $userId
     *
     * @return GmailLabelInterface|null
     */
    public function getLabelOfNameAndUserId(string $labelName, string $userId)
    {
        foreach ($this->labels as $label) {
            /** @var GmailLabelInterface $label */
            if ($label->getName() === $labelName && $label->getUserId() === $userId) {
                return $label;
            }
        }

        return;
    }

    /**
     * @param string $labelName
     *
     * @return bool
     */
    public function hasLabelOfName(string $labelName): bool
    {
        /* @var GmailLabelInterface $label */
        if ($this->getLabelOfName($labelName) instanceof GmailLabelInterface) {
            return true;
        }

        return false;
    }

    /**
     * @param string $userId
     *
     * @return bool
     */
    public function hasLabelOfUserId(string $userId): bool
    {
        /* @var GmailLabelInterface $label */
        if ($this->getLabelOfUserId($userId) instanceof GmailLabelInterface) {
            return true;
        }

        return false;
    }

    /**
     * @param string $labelName
     * @param string $userId
     *
     * @return bool
     */
    public function hasLabelOfNameAndUserId(string $labelName, string $userId): bool
    {
        /* @var GmailLabelInterface $label */
        if ($this->getLabelOfNameAndUserId($labelName, $userId) instanceof GmailLabelInterface) {
            return true;
        }

        return false;
    }
}
