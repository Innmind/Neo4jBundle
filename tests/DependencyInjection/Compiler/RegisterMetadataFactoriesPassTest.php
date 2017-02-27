<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\DependencyInjection\{
    Compiler\RegisterMetadataFactoriesPass,
    InnmindNeo4jExtension
};
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Reference,
    Compiler\CompilerPassInterface
};
use PHPUnit\Framework\TestCase;

class RegisterMetadataFactoriesPassTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CompilerPassInterface::class,
            new RegisterMetadataFactoriesPass
        );
    }

    public function testProcess()
    {
        $container = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $container);
        $this->assertNull(
            (new RegisterMetadataFactoriesPass)->process($container)
        );

        $def = $container->getDefinition('innmind_neo4j.metadata_builder');
        $arg = $def->getArgument(1);

        $this->assertCount(2, $arg);
        $this->assertSame(
            ['aggregate', 'relationship'],
            array_keys($arg)
        );
        $this->assertInstanceOf(Reference::class, $arg['aggregate']);
        $this->assertInstanceOf(Reference::class, $arg['relationship']);
        $this->assertSame(
            'innmind_neo4j.metadata_factory.aggregate',
            (string) $arg['aggregate']
        );
        $this->assertSame(
            'innmind_neo4j.metadata_factory.relationship',
            (string) $arg['relationship']
        );
    }
}
