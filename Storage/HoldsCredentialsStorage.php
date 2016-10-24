<?php

namespace FL\GmailBundle\Storage;

use Buzz\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class HoldsCredentialsStorage
 * @package FL\GmailBundle\Storage
 *
 * This is a service!
 * It allows users of this bundle to set their own service implementing @see CredentialsStorageInterface
 * Such that they can store/retrieve tokens to/from a file, a database, or whatever storage method they prefer.
 */
class HoldsCredentialsStorage
{
    /**
     * @var CredentialsStorageInterface
     */
    private $credentialsStorageService;

    /**
     * StorageService constructor.
     * @param ContainerInterface $container
     * @param string $credentialsStorageServiceName
     */
    public function __construct(ContainerInterface $container, string $credentialsStorageServiceName)
    {
        $this->container = $container;
        $credentialsStorageService = $container->get($credentialsStorageServiceName);
        if (!($credentialsStorageService instanceof CredentialsStorageInterface)) {
            throw new InvalidArgumentException(sprintf(
                'Service of name %s must implement %s',
                $credentialsStorageServiceName,
                CredentialsStorageInterface::class
            ));
        }
        $this->credentialsStorageService = $credentialsStorageService;


    }

    /**
     * @return CredentialsStorageInterface
     */
    public function getCredentialsStorageService(): CredentialsStorageInterface
    {
        return $this->credentialsStorageService;
    }
}