<?php

namespace FL\GmailBundle\Services;

class ServiceAccount
{
    /**
     * @var string[]
     */
    const SCOPES = [
        \Google_Service_Gmail::GMAIL_COMPOSE,
        \Google_Service_Gmail::GMAIL_READONLY,
        \Google_Service_Gmail::GMAIL_SEND,
        \Google_Service_Gmail::GMAIL_MODIFY,
        \Google_Service_Directory::ADMIN_DIRECTORY_USER,
        \Google_Service_Directory::ADMIN_DIRECTORY_DOMAIN_READONLY,
        \Google_Service_Oauth2::USERINFO_PROFILE,
        \Google_Service_Oauth2::USERINFO_EMAIL,
        \Google_Service_Oauth2::PLUS_LOGIN,
        \Google_Service_Oauth2::PLUS_ME,
    ];

    /**
     * @var string
     */
    private $adminUserEmail;

    /**
     * @var string
     */
    private $configFileLocation;

    /**
     * User emails as keys.
     *
     * @var \Google_Client[]
     */
    private $googleClients;

    /**
     * This service should be lazy.
     * In order to check if this service can be called,.
     *
     * @see GoogleClientStatus
     *
     * @param string $adminUserEmail
     * @param string $configFileLocation
     */
    public function __construct(
        string $adminUserEmail,
        string $configFileLocation
    ) {
        $this->adminUserEmail = $adminUserEmail;
        $this->configFileLocation = $configFileLocation;
        $this->googleClients = [];
    }

    /**
     * @param string $email
     *
     * @return \Google_Client
     */
    public function getGoogleClientForEmail(string $email)
    {
        return $this->createGoogleClient($email);
    }

    /**
     * @return \Google_Client
     */
    public function getGoogleClientForAdmin()
    {
        return $this->createGoogleClient($this->adminUserEmail);
    }

    /**
     * @param string $userEmail
     *
     * @return \Google_Client
     */
    private function createGoogleClient(string $userEmail)
    {
        if (array_key_exists($userEmail, $this->googleClients)) {
            return $this->googleClients[$userEmail];
        }

        $googleClient = new \Google_Client();
        $googleClient->setScopes(static::SCOPES);
        $googleClient->setAuthConfig($this->configFileLocation);
        $googleClient->setSubject($userEmail);

        $this->googleClients[$userEmail] = $googleClient;

        return $googleClient;
    }
}
