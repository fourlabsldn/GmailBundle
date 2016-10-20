<?php

namespace FL\GmailBundle\Services;

use FL\GmailBundle\Token\TokenFileStorage;

class GoogleClientStatus
{
    /**
     * @var TokenFileStorage
     */
    private $tokenFileStorage;

    /**
     * @param TokenFileStorage $tokenFileStorage
     */
    public function __construct(TokenFileStorage $tokenFileStorage)
    {
        $this->tokenFileStorage = $tokenFileStorage;
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        $token = $this->tokenFileStorage->getAccessToken();
        if ( $token !== null && $token !== '') {
            return true;
        }
        return false;
    }
}
