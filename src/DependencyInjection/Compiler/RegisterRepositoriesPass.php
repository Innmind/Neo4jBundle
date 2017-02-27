<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Compiler\CompilerPassInterface,
    Reference
};

final class RegisterRepositoriesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('innmind_neo4j.repository_factory.configurator');
        $ids = $container->findTaggedServiceIds('innmind_neo4j.repository');

        foreach ($ids as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['class'])) {
                    throw new RuntimeException(
                        'The class attribute must be defined'
                    );
                }

                $definition->addMethodCall(
                    'register',
                    [$tag['class'], new Reference($id)]
                );
            }
        }
    }
}
