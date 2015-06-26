<?php

namespace Innmind\Neo4jBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder;
        $root = $treeBuilder->root('innmind_neo4j');

        $rootChildren = $root->children();
        $connPrototype = $rootChildren
            ->arrayNode('connections')
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->children();

        $this->createConnectionTree($connPrototype);
        $this->createConnectionClusterTree($connPrototype);

                    $connPrototype->end()
                ->end()
            ->end();
        $managersPrototype = $rootChildren
            ->arrayNode('managers')
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->children();

        $this->createManagerTree($managersPrototype);

                    $managersPrototype->end()
                ->end()
            ->end()
            ->scalarNode('default_manager')
                ->defaultValue('default')
            ->end();

        return $treeBuilder;
    }

    /**
     * Create a tree to declare a neo4j connection
     *
     * @param NodeBuilder $node
     *
     * @return NodeDefinition
     */
    protected function createConnectionTree(NodeBuilder $node)
    {
        $node
            ->scalarNode('scheme')
                ->defaultValue('http')
            ->end()
            ->scalarNode('host')
                ->defaultValue('localhost')
            ->end()
            ->integerNode('port')
                ->defaultValue(7474)
            ->end()
            ->integerNode('timeout')
                ->defaultValue(60)
                ->info('Value in seconds')
            ->end()
            ->scalarNode('username')->end()
            ->scalarNode('password')->end();

        return $node;
    }

    /**
     * Create a connection cluster tree
     *
     * @param NodeBuilder $node
     *
     * @return NodeDefinition
     */
    protected function createConnectionClusterTree(NodeBuilder $node)
    {
        $cluster = $node
            ->arrayNode('cluster')
                ->prototype('array')
                    ->children();

        $this->createConnectionTree($cluster);

                    $cluster->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * Create a tree to declare a neo4j entity manager
     *
     * @param NodeBuilder $node
     *
     * @return NodeDefinition
     */
    protected function createManagerTree(NodeBuilder $node)
    {
        $node
            ->scalarNode('reader')
                ->isRequired()
            ->end()
            ->scalarNode('connection')
                ->isRequired()
            ->end()
            ->arrayNode('bundles')
                ->defaultValue([])
                ->prototype('scalar')->end()
            ->end();

        return $node;
    }
}
