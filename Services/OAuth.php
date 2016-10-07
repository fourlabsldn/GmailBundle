<?php

namespace FL\GmailBundle\Services;

/**
 * Class OAuth
 * This class lets us communicate with \Google_Service_Oauth2
 * @package FL\GmailBundle\Services
 */
class OAuth
{
    /**
     * @var \Google_Service_Oauth2
     */
    private $oAuth;

    /**
     * Oauth constructor.
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
        return $this->oAuth->userinfo_v2_me->get()->getHd();
    }

    /**
     * @return string
     */
    public function resolveUserId()
    {
        return $this->oAuth->userinfo_v2_me->get()->getId();
    }
}
