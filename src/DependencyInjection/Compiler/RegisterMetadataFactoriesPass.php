<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Compiler\CompilerPassInterface,
    Reference
};

final class RegisterMetadataFactoriesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('innmind_neo4j.metadata_builder');
        $ids = $container->findTaggedServiceIds('innmind_neo4j.metadata_factory');
        $services = [];

        foreach ($ids as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['type'])) {
                    throw new RuntimeException(
                        'The type attribute must be defined'
                    );
                }

                $services[$tag['type']] = new Reference($id);
            }
        }

        $definition->replaceArgument(1, $services);
    }
}
