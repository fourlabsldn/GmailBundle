<?php

namespace FL\GmailBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class SaveAuthorisationEvent
 * @package FL\GmailBundle\Event
 */
class SaveAuthorisationEvent extends Event
{
    const EVENT_NAME = "fl_gmail.save_authorisation";

    /**
     * @var string
     */
    private $authorisationCode;

    /**
     * @param string $authorisationCode
     */
    public function __construct(string $authorisationCode)
    {
        $this->authorisationCode = $authorisationCode;
    }

    /**
     * @return string
     */
    public function getAuthorisationCode()
    {
        return $this->authorisationCode;
    }
}
