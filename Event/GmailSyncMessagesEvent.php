<?php

namespace FL\GmailBundle\Event;

use FL\GmailBundle\Model\Collection\GmailLabelCollection;
use FL\GmailBundle\Model\Collection\GmailMessageCollection;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event should be dispatched at the end of @see FL\GmailBundle\Services\SyncMessages.
 * It will contain a GmailMessageCollection and a GmailLabelCollection.
 *
 * The GmailLabels are inside the GmailMessages,
 * but having a separate collection for them helps with persistence,
 * since it becomes easier to query existing Labels.
 */
class GmailSyncMessagesEvent extends Event
{
    const EVENT_NAME = 'fl_gmail.sync.messages';

    /**
     * @var GmailMessageCollection
     */
    protected $messageCollection;

    /**
     * @var GmailLabelCollection
     */
    protected $labelCollection;

    /**
     * @param GmailMessageCollection $messageCollection
     * @param GmailLabelCollection   $labelCollection
     */
    public function __construct(
        GmailMessageCollection $messageCollection,
        GmailLabelCollection $labelCollection
    ) {
        $this->messageCollection = $messageCollection;
        $this->labelCollection = $labelCollection;
    }

    /**
     * @return GmailMessageCollection
     */
    public function getMessageCollection()
    {
        return $this->messageCollection;
    }

    /**
     * @return GmailLabelCollection
     */
    public function getLabelCollection()
    {
        return $this->labelCollection;
    }
}
