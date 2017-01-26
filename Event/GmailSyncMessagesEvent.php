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
 *
 * The GmailIds are also inside the GmailMessages,
 * but having a separate array with them helps with persistence,
 * since it becomes easier to get a list of $gmailIds that were synced.
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
     * @var string[]
     */
    protected $gmailIds;

    /**
     * @param GmailMessageCollection $messageCollection
     * @param GmailLabelCollection   $labelCollection
     * @param string[]               $gmailIds
     */
    public function __construct(
        GmailMessageCollection $messageCollection,
        GmailLabelCollection $labelCollection,
        array $gmailIds
    ) {
        $this->messageCollection = $messageCollection;
        $this->labelCollection = $labelCollection;
        $this->gmailIds = $gmailIds;
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

    /**
     * @return string[]
     */
    public function getGmailIds()
    {
        return $this->gmailIds;
    }
}
