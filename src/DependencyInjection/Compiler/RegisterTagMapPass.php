<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Compiler\CompilerPassInterface,
    Reference
};

final class RegisterTagMapPass implements CompilerPassInterface
{
    private $service;
    private $tag;

    public function __construct(string $service, string $tag)
    {
        $this->service = $service;
        $this->tag = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition($this->service);
        $ids = $container->findTaggedServiceIds($this->tag);
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

        $definition->replaceArgument(2, $services);
    }
}
