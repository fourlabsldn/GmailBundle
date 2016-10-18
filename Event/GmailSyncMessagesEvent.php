<?php

namespace FL\GmailBundle\Event;

use FL\GmailBundle\Model\Collection\GmailLabelCollection;
use FL\GmailBundle\Model\Collection\GmailMessageCollection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GmailSyncMessagesEvent
 * @package FL\GmailBundle\Event
 */
class GmailSyncMessagesEvent extends Event
{
    const EVENT_NAME = "fl_gmail.sync.messages";

    /**
     * @var GmailMessageCollection
     */
    protected $messageCollection;

    /**
     * @var GmailLabelCollection
     */
    protected $labelCollection;

    /**
     * GmailSyncMessagesEvent constructor.
     * @param GmailMessageCollection $messageCollection
     * @param GmailLabelCollection $labelCollection
     */
    public function __construct(GmailMessageCollection $messageCollection, GmailLabelCollection $labelCollection)
    {
        $this->messageCollection = $messageCollection;
        $this->labelCollection = $labelCollection;
    }

    /**
     * Get messages present at the end of a sync
     * @return GmailMessageCollection
     */
    public function getMessageCollection()
    {
        return $this->messageCollection;
    }

    /**
     * Get labels present at the end of a sync
     * @return GmailLabelCollection
     */
    public function getLabelCollection()
    {
        return $this->labelCollection;
    }
}
