<?php

namespace Innmind\Neo4jBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register all the managers in the registry
 */
class RegisterManagersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $ids = $container->findTaggedServiceIds('innmind_neo4j.manager');
        $def = $container->getDefinition('innmind_neo4j.registry');

        foreach ($ids as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['alias'])) {
                    throw new \LogicException(sprintf(
                        'You must define an alias for the manager "%s"',
                        $id
                    ));
                }

                $def->addMethodCall(
                    'addManager',
                    [$tag['alias'], new Reference($id)]
                );
            }
        }
    }
}
