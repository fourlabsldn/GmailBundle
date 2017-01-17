<?php

namespace FL\GmailBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
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

        $container->setParameter('fl_gmail.admin_user_email', $config['admin_user_email']);
        $container->setParameter('fl_gmail.json_key_location', $config['json_key_location']);
        $container->setParameter('fl_gmail.gmail_message_class', $config['gmail_message_class']);
        $container->setParameter('fl_gmail.gmail_label_class', $config['gmail_label_class']);
        $container->setParameter('fl_gmail.gmail_history_class', $config['gmail_history_class']);
        $container->setParameter('fl_gmail.gmail_ids_class', $config['gmail_ids_class']);
        if ($config['psr6_caching_service']) {
            $container->setParameter('fl_gmail.psr6_caching_service', $config['psr6_caching_service']);
            $container->setAlias('fl_gmail.psr6_caching_service', $config['psr6_caching_service']);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
