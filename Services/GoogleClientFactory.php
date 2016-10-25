<?php

namespace FL\GmailBundle\Services;

use FL\GmailBundle\Token\AccessToken;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GoogleClientFactory
 * @package FL\GmailBundle\Services
 */
class GoogleClientFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * @var CacheItemPoolInterface|null
     */
    private $cache;

    /**
     * GoogleClientFactory constructor.
     * @param ContainerInterface $container
     * @param AccessToken $accessToken
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(ContainerInterface $container, AccessToken $accessToken, CacheItemPoolInterface $cache = null)
    {
        $this->container = $container;
        $this->accessToken = $accessToken;
        $this->cache = $cache;
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