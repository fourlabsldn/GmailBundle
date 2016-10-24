<?php

namespace FL\GmailBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class FLGmailExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('fl_gmail.application_name', $config['application_name']);
        $container->setParameter('fl_gmail.client_id', $config['client_id']);
        $container->setParameter('fl_gmail.client_secret', $config['client_secret']);
        $container->setParameter('fl_gmail.redirect_uri', $config['redirect_uri']);
        $container->setParameter('fl_gmail.gmail_message_class', $config['gmail_message_class']);
        $container->setParameter('fl_gmail.gmail_label_class', $config['gmail_label_class']);
        $container->setParameter('fl_gmail.gmail_history_class', $config['gmail_history_class']);
        $container->setParameter('fl_gmail.gmail_ids_class', $config['gmail_ids_class']);
        $container->setParameter('fl_gmail.redirect_route_after_save_authorisation', $config['redirect_route_after_save_authorisation']);

        // The scopes to be used for the \Google_Client instance need to be set here
        // because we need access to the \Google_Service_Gmail constants.
        $container->setParameter('fl_gmail.gmail_scopes', [
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
        ]);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
