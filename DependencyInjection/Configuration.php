<?php

namespace FL\GmailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @see http://symfony.com/doc/current/cookbook/bundles/configuration.html}
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
                ->scalarNode('admin_user_email')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('json_key_location')
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
                ->scalarNode('psr6_caching_service')
                    ->defaultValue(null)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
