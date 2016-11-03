<?php

namespace FL\GmailBundle\Event;

use FL\GmailBundle\Model\GmailHistoryInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GmailSyncHistoryEvent.
 */
class GmailSyncHistoryEvent extends Event
{
    const EVENT_NAME = 'fl_gmail.sync.history';

    /**
     * @var GmailHistoryInterface
     */
    protected $history;

    /**
     * GmailSyncHistoryEvent constructor.
     *
     * @param GmailHistoryInterface $history
     */
    public function __construct(GmailHistoryInterface $history)
    {
        $this->history = $history;
    }

    /**
     * Get Gmail history received from dispatcher.
     *
     * @return GmailHistoryInterface
     */
    public function getHistory()
    {
        return $this->history;
    }
}
