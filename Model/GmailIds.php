<?php

namespace FL\GmailBundle\Model;

class GmailIds implements GmailIdsInterface
{
    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string[]
     */
    protected $gmailIds;

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
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain(string $domain): GmailIdsInterface
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * Do not include a gmailId more than once.
     * This might be a large array, so do not use array_unique.
     * Note that this still preserves the order of the ids inside $gmailIds.
     * @see http://stackoverflow.com/questions/8321620/array-unique-vs-array-flip
     *
     * {@inheritdoc}
     */
    public function setGmailIds(array $gmailIds = null): GmailIdsInterface
    {
        $this->gmailIds = array_keys(array_flip($gmailIds));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGmailIds(int $limit = null): array
    {
        return array_slice($this->gmailIds, 0, $limit);
    }
}
