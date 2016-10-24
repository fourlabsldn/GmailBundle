<?php

namespace FL\GmailBundle\Token;

/**
 * Class AccessToken
 * @package FL\GmailBundle\Token
 */
class AccessToken
{
    /**
     * @var array
     */
    private $accessToken = null;

    /**
     * Get the current access token as a json string.
     * @return string|null
     */
    public function getToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return AccessToken
     */
    public function setToken(string $accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}
