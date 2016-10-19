<?php

namespace FL\GmailBundle\Model;

/**
 * Class GmailIds
 * @package FL\GmailBundle\Model
 */
class GmailIds implements GmailIdsInterface
{
    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string[]|null
     */
    protected $gmailIds = [];

    /**
     * {@inheritdoc}
     */
    public function setUserId(string $userId): GmailIdsInterface
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * {@inheritdoc}
     */
    public function setGmailIds(array $gmailIds = null): GmailIdsInterface
    {
        $this->gmailIds = $gmailIds;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGmailIds(int $limit = null)
    {
        if (!is_array($this->gmailIds)) {
            return null;
        }
        return array_slice($this->gmailIds, 0, $limit);
    }
}