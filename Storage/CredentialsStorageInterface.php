<?php

namespace FL\GmailBundle\Storage;

/**
 * Interface CredentialsStorage
 *
 * Interface for the persistence of the auth code and the access token,
 * providing methods to get and persist them as needed.
 *
 * Meant to be implemented as a service.
 * Also @see HoldsCredentialsStorage
 *
 * @package FL\GmailBundle\Token
 */
interface CredentialsStorageInterface
{
    /**
     * Persist access token.
     * @param array $tokenArray
     */
    public function persistTokenArray(array $tokenArray);

    /**
     * Get the access token from storage as an array.
     * @return array|null
     */
    public function getTokenArray();

    /**
     * Persist the auth code.
     * @param string $authCode
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
     */
    public function deleteAuthCode();
}