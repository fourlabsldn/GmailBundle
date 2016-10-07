<?php

namespace FL\GmailBundle\Token;

/**
 * Class AccessToken
 * Contains the current, validated access token needed
 * by the Google Client to authorise access to the API.
 * @package FL\GmailBundle\Token
 */
class AccessToken
{
    /**
     * Current, valid access token for the Google Client.
     * @var array
     */
    private $accessToken;

    /**
     * AccessToken constructor.
     * Access token must be a json encoded string.
     * @param string $accessToken
     */
    public function __construct(string $accessToken = null)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Get the current access token as a json string.
     * @return string|null
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
}
