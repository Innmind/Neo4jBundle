<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\Exception\LogicException;
use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Reference
};

final class RegisterIdentityGeneratorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $ids = $container->findTaggedServiceIds('innmind_neo4j.identity.generator');
        $definition = $container->getDefinition('innmind_neo4j.generators');
        $generators = [];

        foreach ($ids as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['generates'])) {
                    throw new LogicException(sprintf(
                        'The "generates" attribute must be set for "%s"',
                        $id
                    ));
                }

                $generators[$attributes['generates']] = new Reference($id);
            }
        }

        $definition->replaceArgument(2, $generators);
    }
}
