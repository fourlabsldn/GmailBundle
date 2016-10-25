<?php

namespace FL\GmailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fl_gmail')->isRequired();

        $rootNode
            ->children()
                ->scalarNode('application_name')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('client_id')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('client_secret')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('redirect_uri')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('gmail_message_class')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('gmail_label_class')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('gmail_history_class')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('gmail_ids_class')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('redirect_route_after_save_authorisation')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('credentials_storage_service')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('psr6_caching_service')
                    ->cannotBeEmpty()
                    ->defaultValue('')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
