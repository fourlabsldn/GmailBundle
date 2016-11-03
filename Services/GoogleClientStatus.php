<?php

namespace FL\GmailBundle\Services;

use FL\GmailBundle\Token\AccessToken;

class GoogleClientStatus
{
    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * @param AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        if (!empty($this->accessToken->getJsonToken())) {
            return true;
        }

        return false;
    }
}
