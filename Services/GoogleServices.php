<?php

namespace FL\GmailBundle\Services;

/**
 * This service can get google services for any account.
 */
class GoogleServices
{
    /**
     * @var Directory
     */
    private $directory;

    /**
     * @var ServiceAccount
     */
    private $serviceAccount;

    /**
     * Keys are user emails.
     *
     * @var \Google_Service_Gmail[]
     */
    private $googleServiceGmails;

    /**
     * @param Directory      $directory
     * @param ServiceAccount $serviceAccount
     */
    public function __construct(
        Directory $directory,
        ServiceAccount $serviceAccount
    ) {
        $this->directory = $directory;
        $this->serviceAccount = $serviceAccount;
        $this->googleServiceGmails = [];
    }

    /**
     * @param string $userId
     *
     * @return \Google_Service_Gmail
     */
    public function getGoogleServiceGmailForUserId(string $userId)
    {
        $email = $this->directory->resolvePrimaryEmailFromUserId($userId);
        if (array_key_exists($email, $this->googleServiceGmails)) {
            return $this->googleServiceGmails[$email];
        }

        $googleServiceGmail = new \Google_Service_Gmail($this->serviceAccount->getGoogleClientForEmail($email));
        $this->googleServiceGmails[$email] = $googleServiceGmail;

        return $googleServiceGmail;
    }

    /**
     * @param string $email
     *
     * @return \Google_Service_Gmail
     */
    public function getGoogleServiceGmailForEmail(string $email)
    {
        if (array_key_exists($email, $this->googleServiceGmails)) {
            return $this->googleServiceGmails[$email];
        }

        $googleServiceGmail = new \Google_Service_Gmail($this->serviceAccount->getGoogleClientForEmail($email));
        $this->googleServiceGmails[$email] = $googleServiceGmail;

        return $googleServiceGmail;
    }
}
