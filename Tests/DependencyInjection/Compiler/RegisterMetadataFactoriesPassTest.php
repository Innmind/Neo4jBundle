<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Tests\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\DependencyInjection\{
    Compiler\RegisterMetadataFactoriesPass,
    InnmindNeo4jExtension
};
use Innmind\Neo4j\ONM\Metadata\{
    Aggregate,
    Relationship
};
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Reference,
    Compiler\CompilerPassInterface
};

class RegisterMetadataFactoriesPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $c = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $c);
        $this->assertSame(
            null,
            ($p = new RegisterMetadataFactoriesPass)->process($c)
        );
        $this->assertInstanceOf(CompilerPassInterface::class, $p);

        $def = $c->getDefinition('innmind_neo4j.metadata_builder');
        $arg = $def->getArgument(1);

        $this->assertSame(2, count($arg));
        $this->assertSame(
            [Aggregate::class, Relationship::class],
            array_keys($arg)
        );
        $this->assertInstanceOf(Reference::class, $arg[Aggregate::class]);
        $this->assertInstanceOf(Reference::class, $arg[Relationship::class]);
        $this->assertSame(
            'innmind_neo4j.metadata_factory.aggregate',
            (string) $arg[Aggregate::class]
        );
        $this->assertSame(
            'innmind_neo4j.metadata_factory.relationship',
            (string) $arg[Relationship::class]
        );
    }
}
