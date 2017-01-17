<?php

namespace FL\GmailBundle\Services;

/**
 * This service predicts the status of @see ServiceAccount::getGoogleClientForAdmin()
 * Thus it also predicts if @see Directory
 * or @see OAuth can be called.
 */
class GoogleClientStatus
{
    /**
     * @var string
     */
    private $configFileLocation;

    /**
     * @param string $configFileLocation
     */
    public function __construct(string $configFileLocation)
    {
        $this->configFileLocation = $configFileLocation;
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        if (is_string($this->configFileLocation)) {
            if (!file_exists($this->configFileLocation)) {
                return false;
            }

            $json = file_get_contents($this->configFileLocation);

            if (!json_decode($json, true)) {
                return false;
            }
        }

        return true;
    }
}
