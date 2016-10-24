<?php
namespace FL\GmailBundle\Token;

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
     * @var array|null
     */
    private $accessToken;

    /**
     * Instance of Google Client required to process auth codes, access tokens and refresh tokens.
     * @var \Google_Client
     */
    private $unauthorisedClient;

    /**
     * TokenStorage to handle persistence of the auth code and access token.
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * AccessTokenFactory constructor.
     * @param \Google_Client $unauthorisedClient
     * @param TokenStorageInterface $storage
     */
    public function __construct(\Google_Client $unauthorisedClient, TokenStorageInterface $storage)
    {
        $this->unauthorisedClient = $unauthorisedClient;
        $this->storage = $storage;
    }

    /**
     * Create an access token and make sure it has not expired.
     * @param void
     * @return AccessToken
     */
    public function createAccessToken(): AccessToken
    {
        $this->accessToken = null;
        $getToken = $this->storage->getAccessToken();
        $getCode = $this->storage->getAuthCode();

        // There's an access token available
        if (is_array($getToken)) {
            $this->accessToken = $getToken;
        }
        // There's no access token, but there's an auth code that can be used
        // to fetch a new access token from the unauthorisedClient
        elseif (!empty($getCode)) {
            $this->accessToken = $this->unauthorisedClient->fetchAccessTokenWithAuthCode($getCode);
            $this->storage->persistAccessToken($this->accessToken);
            $this->storage->deleteAuthCode();
        }

        // Ensure that the access token has not expired. If it has, obtain a new one
        // using the refresh token and save the new access token to storage.
        $this->unauthorisedClient->setAccessToken($this->getAccessTokenAsJson());
        if ($this->unauthorisedClient->isAccessTokenExpired()) {
            $refreshToken = $this->unauthorisedClient->getRefreshToken();
            $this->accessToken = $this->unauthorisedClient->fetchAccessTokenWithRefreshToken($refreshToken);
            // The refresh token needs to be saved manually because it's not returned along with
            // the rest of the access token in function fetchAccessTokenWithRefreshToken()
            $this->accessToken['refresh_token'] = $refreshToken;
            $this->storage->persistAccessToken($this->accessToken);
        }

        return (new AccessToken())->setJsonToken($this->getAccessTokenAsJson());
    }

    /**
     * @return string|null
     */
    private function getAccessTokenAsJson()
    {
        return json_encode($this->accessToken) ?? null;
    }
}