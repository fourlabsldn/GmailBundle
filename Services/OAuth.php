<?php

namespace FL\GmailBundle\Services;

/**
 * This service communicates with @see Google_Service_Oauth2.
 *
 * This service exists for a single \GoogleClient
 *
 * @see ServiceAccount::getGoogleClientForAdmin()
 */
class OAuth
{
    /**
     * @var \Google_Service_Oauth2
     */
    private $oAuth;

    /**
     * @var string
     */
    private $domainCache;

    /**
     * @param \Google_Service_Oauth2 $oAuth
     */
    public function __construct(\Google_Service_Oauth2 $oAuth)
    {
        $this->oAuth = $oAuth;
    }

    /**
     * @return string
     */
    public function resolveDomain()
    {
        if (!isset($this->domainCache)) {
            $this->domainCache = $this->oAuth->userinfo_v2_me->get()->getHd();
        }

        return $this->domainCache;
    }
}
