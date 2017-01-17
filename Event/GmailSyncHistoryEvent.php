<?php

namespace FL\GmailBundle\Event;

use FL\GmailBundle\Model\GmailHistoryInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event should be dispatched at the end of @see \FL\GmailBundle\Services\SyncGmailIds.
 * It will contain a GmailIdsInterface object.
 */
class GmailSyncHistoryEvent extends Event
{
    const EVENT_NAME = 'fl_gmail.sync.history';

    /**
     * @var GmailHistoryInterface
     */
    protected $history;

    /**
     * @param GmailHistoryInterface $history
     */
    public function __construct(GmailHistoryInterface $history)
    {
        $this->history = $history;
    }

    /**
     * @return GmailHistoryInterface
     */
    public function getHistory()
    {
        return $this->history;
    }
}
