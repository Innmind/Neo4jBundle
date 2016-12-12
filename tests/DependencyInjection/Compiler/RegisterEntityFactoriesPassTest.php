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

class RegisterEntityFactoriesPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $c = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $c);
        $this->assertSame(
            null,
            ($p = new RegisterEntityFactoriesPass)->process($c)
        );
        $this->assertInstanceOf(CompilerPassInterface::class, $p);

        $definition = $c->getDefinition('innmind_neo4j.entity_factory.resolver');
        $calls = $definition->getMethodCalls();

        $this->assertSame(2, count($calls));
        $this->assertSame('register', $calls[0][0]);
        $this->assertInstanceOf(Reference::class, $calls[0][1][0]);
        $this->assertSame(
            'innmind_neo4j.entity_factory.aggregate',
            (string) $calls[0][1][0]
        );
        $this->assertSame('register', $calls[1][0]);
        $this->assertInstanceOf(Reference::class, $calls[1][1][0]);
        $this->assertSame(
            'innmind_neo4j.entity_factory.relationship',
            (string) $calls[1][1][0]
        );
    }
}
