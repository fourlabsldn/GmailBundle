<?php

namespace FL\GmailBundle\Token;

/**
 * Interface TokenStorageInterface
 *
 * Interface for the persistence of the auth code and the access token,
 * providing methods to get and persist them as needed.
 *
 * @package FL\GmailBundle\Token
 */
interface TokenStorageInterface
{
    /**
     * Persist access token.
     * @param array $accessToken
     * @return void
     */
    public function persistAccessToken(array $accessToken);

    /**
     * Get the access token from storage as a json array.
     * @return array|null
     */
    public function getAccessToken();

    /**
     * Persist the auth code.
     * @param string $authCode
     * @return void
     */
    public function persistAuthCode(string $authCode);

    /**
     * Get the auth code from storage.
     * @return string|null
     */
    public function getAuthCode();

    /**
     * The auth code can only be redeemed once, this is provided to be
     * able to delete once it's been used.
     * @return void
     */
    public function deleteAuthCode();
}