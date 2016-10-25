<?php

namespace FL\GmailBundle\Services;

use FL\GmailBundle\Token\AccessToken;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

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
     * @param string $cacheServiceName
     */
    public function __construct(ContainerInterface $container, AccessToken $accessToken, string $cacheServiceName)
    {
        $this->container = $container;
        $this->accessToken = $accessToken;
        // doing this in construction, so that the validation occurs while creating the container
        try {
            $cache = $this->container->get($cacheServiceName);
            if ($cache instanceof CacheItemPoolInterface) {
                $this->cache = $cache;
            }
            else {
                throw new \InvalidArgumentException(sprintf(
                    "Caching Service in GmailBundle expected to be an instance of ",
                    CacheItemPoolInterface::class
                ));
            }
        } catch (ServiceNotFoundException $exception) {
            $this->cache = null;
        }
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