<?php

namespace FL\GmailBundle\Token;

use FL\GmailBundle\Exception\MissingTokenException;

/**
 * Class AccessTokenFactory
 *
 * Builds a valid, authorised access token that must be provided to the Google Client
 * to allow access to the APIs. This is done in a series of steps:
 * - If there is an access token already in a file, make sure it hasn't expired and set it.
 * - If there's no access token file but there's an auth code file, sent the auth code to the
 *   Google Client to generate the access token and persist it.
 * - Otherwise, this user needs a link to the SaveAccessTokenAction, which is used by Google
 *   to provide an auth code which will later generate an access token.
 *
 * @package FL\GmailBundle\Token
 */
class AccessTokenFactory
{
    /**
     * Current and valid access token.
     * @var string|null
     */
    private $accessToken;

    /**
     * Instance of Google Client required to process auth codes, access tokens and refresh tokens.
     * @var \Google_Client
     */
    private $client;

    /**
     * TokenStorage to handle persistence of the auth code and access token.
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * AccessTokenFactory constructor.
     * @param \Google_Client $client
     * @param TokenStorageInterface $storage
     */
    public function __construct(\Google_Client $client, TokenStorageInterface $storage)
    {
        $this->client = $client;
        $this->storage = $storage;
    }

    /**
     * Get current valid access token as a Json object.
     * @return string|null
     */
    private function getAccessTokenJson()
    {
        return json_encode($this->accessToken) ?? null;
    }

    /**
     * Create an access token and make sure it has not expired.
     * @param void
     * @return AccessToken
     * @throws MissingTokenException
     */
    public function createAccessToken(): AccessToken
    {
        $this->accessToken = null;

        // There's an access token file available
        if (($token = $this->storage->getAccessToken())) {
            $this->accessToken = $token;
        }

        // There's no access token, but there's an auth code that can be used
        // to fetch a new access token from the client
        elseif (($code = $this->storage->getAuthCode())) {
            $this->accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->storage->persistAccessToken($this->accessToken);
            $this->storage->deleteAuthCode();
        }

        $this->verifyAccessToken();

        return new AccessToken($this->getAccessTokenJson());
    }

    /**
     * Ensure that the access token has not expired. If it has, obtain a new one
     * using the refresh token and save the new access token to storage.
     * @param void
     * @return void
     * @throws MissingTokenException
     */
    private function verifyAccessToken()
    {
        $this->client->setAccessToken($this->getAccessTokenJson());

        if ($this->getAccessTokenJson() === "null") {
            throw new MissingTokenException();
        }

        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $this->client->getRefreshToken();
            $this->accessToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);

            // The refresh token needs to be saved manually because it's not returned along with
            // the rest of the access token in function fetchAccessTokenWithRefreshToken()
            $this->accessToken['refresh_token'] = $refreshToken;

            $this->storage->persistAccessToken($this->accessToken);
        }
    }
}
