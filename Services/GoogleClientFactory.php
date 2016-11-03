<?php

namespace FL\GmailBundle\Services;

use FL\GmailBundle\Token\AccessToken;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class GoogleClientFactory.
 */
class GoogleClientFactory
{
    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * @var CacheItemPoolInterface|null
     */
    private $cache;

    /**
     * @param AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @param CacheItemPoolInterface|null $cache
     *
     * @return $this
     */
    public function setCache(CacheItemPoolInterface $cache = null)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @return \Google_Client
     */
    public function createGoogleClient(): \Google_Client
    {
        $client = new \Google_Client();
        $client->setAccessToken($this->accessToken->getJsonToken());

        if ($this->cache) {
            $client->setCache($this->cache);
        }

        return $client;
    }
}
