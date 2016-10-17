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
                ->scalarNode('redirect_route_after_token_saved')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('access_token_path')
                    ->cannotBeEmpty()
                    ->defaultValue('%kernel.root_dir%/../var/credentials/access-token.json')
                ->end()
                ->scalarNode('auth_code_path')
                    ->cannotBeEmpty()
                    ->defaultValue('%kernel.root_dir%/../var/credentials/auth-code.json')
                ->end()
                ->scalarNode('gmail_message_class')
                    ->cannotBeEmpty()
                    ->defaultValue('\FL\GmailBundle\Model\GmailMessage')
                ->end()
                ->scalarNode('gmail_label_class')
                    ->cannotBeEmpty()
                    ->defaultValue('\FL\GmailBundle\Model\Label')
                ->end()
                ->scalarNode('gmail_history_class')
                    ->cannotBeEmpty()
                    ->defaultValue('\FL\GmailBundle\Model\GmailHistory')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
