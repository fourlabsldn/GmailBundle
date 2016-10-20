<?php

namespace FL\GmailBundle\Services;

use FL\GmailBundle\Exception\MissingTokenException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ClientStatus
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        try {
            $this->container->get('fl_gmail.access_token')->getAccessToken();
        } catch (MissingTokenException $e) {
            return false;
        }
        return true;
    }
}
