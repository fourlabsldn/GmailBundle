<?php

namespace FL\GmailBundle\Event;

use FL\GmailBundle\Model\GmailIdsInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GmailMessageUpdatedEvent
 * @package FL\GmailBundle\Event
 */
class GmailIdsResolvedEvent extends Event
{
    const EVENT_NAME = "fl_gmail.gmail_ids.resolved";

    /**
     * @var GmailIdsInterface
     */
    private $gmailIds;

    /**
     * GmailSyncEndEvent constructor.
     * @param GmailIdsInterface $gmailIds
     */
    public function __construct(GmailIdsInterface $gmailIds)
    {
        $this->gmailIds = $gmailIds;
    }

    /**
     * Get gmailIds that have been resolved
     * @return GmailIdsInterface
     */
    public function getGmailIds()
    {
        return $this->gmailIds;
    }
}
