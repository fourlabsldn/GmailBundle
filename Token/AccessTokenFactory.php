<?php

namespace FL\GmailBundle\Token;

use FL\GmailBundle\Storage\CredentialsStorageInterface;

/**
 * Class AccessTokenFactory.
 */
class AccessTokenFactory
{
    /**
     * @var \Google_Client
     */
    private $unauthorisedClient;

    /**
     * @var CredentialsStorageInterface
     */
    private $storage;

    /**
     * AccessTokenFactory constructor.
     *
     * @param \Google_Client              $unauthorisedClient
     * @param CredentialsStorageInterface $storage
     */
    public function __construct(\Google_Client $unauthorisedClient, CredentialsStorageInterface $storage)
    {
        $this->unauthorisedClient = $unauthorisedClient;
        $this->storage = $storage;
    }

    /**
     * @return AccessToken
     */
    public function createAccessToken(): AccessToken
    {
        $persistedAuthCode = $this->storage->getAuthCode();
        $persistedTokenArray = $this->storage->getTokenArray();

        if (empty($persistedAuthCode) && is_null($persistedTokenArray)) {
            return new AccessToken();
        }

        if (!empty($persistedAuthCode)) { // There's a new auth code
            $newTokenArray = $this->unauthorisedClient->fetchAccessTokenWithAuthCode($persistedAuthCode);
        } else { // There's an access token available
            $newTokenArray = $persistedTokenArray;
        }

        $newTokenArray = $this->refreshTokenArray($newTokenArray);
        if ($newTokenArray !== $persistedTokenArray) {
            $this->storage->persistTokenArray($newTokenArray);
        }
        $this->storage->deleteAuthCode();

        return (new AccessToken())->setJsonToken(json_encode($newTokenArray));
    }

    /**
     * If the token has expired, obtain a new one.
     *
     * @param array $tokenArray
     *
     * @return array
     */
    private function refreshTokenArray(array $tokenArray)
    {
        $this->unauthorisedClient->setAccessToken(json_encode($tokenArray));
        if ($this->unauthorisedClient->isAccessTokenExpired()) {
            $refreshToken = $this->unauthorisedClient->getRefreshToken();
            $tokenArray = $this->unauthorisedClient->fetchAccessTokenWithRefreshToken($refreshToken);
            // The refresh token needs to be added manually because it's not returned along with
            // the rest of the access token in function fetchAccessTokenWithRefreshToken()
            $tokenArray['refresh_token'] = $refreshToken;
        }

        return $tokenArray;
    }
}
