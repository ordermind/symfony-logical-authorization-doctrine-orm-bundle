<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('ordermind_logical_authorization_doctrine_orm');

        $rootNode
            ->children()
                ->booleanNode('check_lazy_loaded_entities')
                  ->defaultFalse()
                  ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
