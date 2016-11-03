<?php

namespace FL\GmailBundle\Services;

/**
 * Class OAuth
 * This class lets us communicate with \Google_Service_Oauth2.
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
     * @var string
     */
    private $userIdCache;

    /**
     * Oauth constructor.
     *
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

    /**
     * @return string
     */
    public function resolveUserId()
    {
        if (!isset($this->userIdCache)) {
            $this->userIdCache = $this->oAuth->userinfo_v2_me->get()->getId();
        }

        return $this->userIdCache;
    }
}
