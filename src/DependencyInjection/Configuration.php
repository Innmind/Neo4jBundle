<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\DependencyInjection;

use Symfony\Component\Config\Definition\{
    Builder\TreeBuilder,
    Builder\NodeBuilder,
    ConfigurationInterface
};

final class Configuration implements ConfigurationInterface
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
                    ->addDefaultsIfNotSet()
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
                ->scalarNode('metadata_configuration')
                    ->info('The service to use to validate a metadata configuration')
                    ->defaultValue('innmind_neo4j.metadata_builder.configuration')
                ->end()
                ->scalarNode('clock')
                    ->info('The clock service to use (instance of TimeContinuumInterface)')
                    ->defaultValue('innmind_neo4j.clock')
                ->end()
                ->scalarNode('event_bus')
                    ->info('The event bus service to use for persisters')
                    ->defaultValue('innmind_neo4j.event_bus.null')
                ->end()
                ->scalarNode('dbal_connection')
                    ->info('The dbal connection service to use')
                    ->defaultValue('innmind_neo4j.dbal.connection.logger')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
