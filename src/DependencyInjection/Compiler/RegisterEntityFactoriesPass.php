<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Compiler\CompilerPassInterface,
    Reference
};

final class RegisterEntityFactoriesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('innmind_neo4j.entity_factory.resolver');
        $ids = $container->findTaggedServiceIds('innmind_neo4j.entity_factory');

        foreach ($ids as $id => $tags) {
            $definition->addArgument(
                new Reference($id)
            );
        }
    }
}
