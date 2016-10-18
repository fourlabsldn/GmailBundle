<?php

namespace FL\GmailBundle\Event;

use FL\GmailBundle\Model\GmailIdsInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GmailMessageUpdatedEvent
 * @package FL\GmailBundle\Event
 */
class GmailSyncIdsEvent extends Event
{
    const EVENT_NAME = "fl_gmail.sync.ids";

    /**
     * @var GmailIdsInterface
     *
     * Name to avoid confusion, imagine calling
     * $gmailSyncIdsEvent->getGmailIds()->getGmailIds()
     * With this name, we get
     * $gmailSyncIdsEvent->getGmailIdsObject()->getGmailIds()
     */
    private $gmailIdsObject;

    /**
     * GmailSyncEndEvent constructor.
     * @param GmailIdsInterface $gmailIdsObject
     */
    public function __construct(GmailIdsInterface $gmailIdsObject)
    {
        $this->gmailIdsObject = $gmailIdsObject;
    }

    /**
     * Get gmailIds that have been resolved
     * @return GmailIdsInterface
     */
    public function getGmailIdsObject() {
        return $this->gmailIdsObject;
    }
}
