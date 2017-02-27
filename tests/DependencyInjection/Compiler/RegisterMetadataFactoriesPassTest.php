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
