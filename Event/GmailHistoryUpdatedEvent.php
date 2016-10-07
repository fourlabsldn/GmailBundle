<?php

namespace FL\GmailBundle\Event;

use FL\GmailBundle\Model\GmailHistoryInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GmailHistoryUpdatedEvent
 * @package FL\GmailBundle\Event
 */
class GmailHistoryUpdatedEvent extends Event
{
    const EVENT_NAME = "fl_gmail.gmail_history.updated";

    /**
     * @var GmailHistoryInterface
     */
    protected $history;

    /**
     * GmailHistoryUpdatedEvent constructor.
     * @param GmailHistoryInterface $history
     */
    public function __construct(GmailHistoryInterface $history)
    {
        $this->history = $history;
    }

    /**
     * Get Gmail history received from dispatcher.
     * @return GmailHistoryInterface
     */
    public function getHistory()
    {
        return $this->history;
    }
}
