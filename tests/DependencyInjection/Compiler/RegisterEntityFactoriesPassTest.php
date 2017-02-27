<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\DependencyInjection\{
    Compiler\RegisterEntityFactoriesPass,
    InnmindNeo4jExtension
};
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Reference,
    Definition,
    Compiler\CompilerPassInterface
};
use PHPUnit\Framework\TestCase;

class RegisterEntityFactoriesPassTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CompilerPassInterface::class,
            new RegisterEntityFactoriesPass
        );
    }

    public function testProcess()
    {
        $container = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $container);
        $this->assertNull(
            (new RegisterEntityFactoriesPass)->process($container)
        );

        $definition = $container->getDefinition('innmind_neo4j.entity_factory.resolver');
        $arguments = $definition->getArguments();

        $this->assertCount(2, $arguments);
        $this->assertInstanceOf(Reference::class, $arguments[0]);
        $this->assertSame(
            'innmind_neo4j.entity_factory.aggregate',
            (string) $arguments[0]
        );
        $this->assertInstanceOf(Reference::class, $arguments[1]);
        $this->assertSame(
            'innmind_neo4j.entity_factory.relationship',
            (string) $arguments[1]
        );
    }
}
