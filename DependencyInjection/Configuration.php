<?php

namespace Innmind\Neo4jBundle\DependencyInjection;

use Symfony\Component\Config\Definition\{
    Builder\TreeBuilder,
    Builder\NodeBuilder,
    ConfigurationInterface
};

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder;
        $root = $treeBuilder->root('innmind_neo4j');

        $root
            ->children()
                ->arrayNode('connection')
                    ->children()
                        ->scalarNode('host')
                            ->defaultValue('localhost')
                        ->end()
                        ->scalarNode('scheme')
                            ->defaultValue('https')
                        ->end()
                        ->integerNode('port')
                            ->defaultValue(7474)
                        ->end()
                        ->scalarNode('user')
                            ->defaultValue('neo4j')
                        ->end()
                        ->scalarNode('password')
                            ->defaultValue('neo4j')
                        ->end()
                        ->integerNode('timeout')
                            ->defaultValue(60)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('types')
                    ->defaultValue([])
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('persister')
                    ->info('The service name to use in the unit of work to persist the entity container')
                    ->defaultValue('innmind_neo4j.persister.delegation')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
