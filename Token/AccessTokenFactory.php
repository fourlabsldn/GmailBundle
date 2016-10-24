<?php
namespace FL\GmailBundle\Token;

use FL\GmailBundle\Storage\CredentialsStorageInterface;

/**
 * Class AccessTokenFactory
 * @package FL\GmailBundle\Token
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
    private $credentialsStorage;

    /**
     * AccessTokenFactory constructor.
     * @param \Google_Client $unauthorisedClient
     * @param CredentialsStorageInterface $credentialsStorage
     */
    public function __construct(\Google_Client $unauthorisedClient, CredentialsStorageInterface $credentialsStorage)
    {
        $this->unauthorisedClient = $unauthorisedClient;
        $this->credentialsStorage = $credentialsStorage;
    }

    /**
     * @return AccessToken
     */
    public function createAccessToken(): AccessToken
    {
        $persistedAuthCode = $this->credentialsStorage->getAuthCode();
        $persistedTokenArray = $this->credentialsStorage->getTokenArray();

        if (empty($persistedAuthCode) && is_null($persistedTokenArray)) {
            return new AccessToken();
        }

        if (!empty($persistedAuthCode)) { // There's a new auth code
            $newTokenArray = $this->unauthorisedClient->fetchAccessTokenWithAuthCode($persistedAuthCode);
        }
        else { // There's an access token available
            $newTokenArray = $persistedTokenArray;
        }

        $newTokenArray = $this->refreshTokenArray($newTokenArray);
        if ($newTokenArray !== $persistedTokenArray) {
            $this->credentialsStorage->persistTokenArray($newTokenArray);
        }
        $this->credentialsStorage->deleteAuthCode();

        return (new AccessToken())->setJsonToken(json_encode($newTokenArray));
    }

    /**
     * If the token has expired, obtain a new one
     * @param array $tokenArray
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