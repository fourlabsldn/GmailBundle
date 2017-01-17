<?php

namespace FL\GmailBundle\Event;

use FL\GmailBundle\Model\GmailIdsInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event should be dispatched at the end of @see \FL\GmailBundle\Services\SyncGmailIds.
 * It will contain a GmailIdsInterface object.
 */
class GmailSyncIdsEvent extends Event
{
    const EVENT_NAME = 'fl_gmail.sync.ids';

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
     * @param GmailIdsInterface $gmailIdsObject
     */
    public function __construct(GmailIdsInterface $gmailIdsObject)
    {
        $this->gmailIdsObject = $gmailIdsObject;
    }

    /**
     * @return GmailIdsInterface
     */
    public function getGmailIdsObject()
    {
        return $this->gmailIdsObject;
    }
}
