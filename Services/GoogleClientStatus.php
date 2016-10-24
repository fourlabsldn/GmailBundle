<?php

namespace FL\GmailBundle\Services;

use FL\GmailBundle\Token\AccessToken;

class GoogleClientStatus
{
    /**
     * @var AccessToken
     */
    private $AccessToken;

    /**
     * @param AccessToken $AccessToken
     */
    public function __construct(AccessToken $AccessToken)
    {
        $this->AccessToken = $AccessToken;
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        if ( !empty($this->AccessToken->getToken())) {
            return true;
        }
        return false;
    }
}
