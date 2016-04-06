<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Tests\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\DependencyInjection\{
    Compiler\RegisterTagMapPass,
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

class RegisterTagMapPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessTranslatorResult()
    {
        $c = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $c);
        $this->assertSame(
            null,
            ($p = new RegisterTagMapPass(
                'innmind_neo4j.translator.result',
                'innmind_neo4j.translation.result'
            ))->process($c)
        );
        $this->assertInstanceOf(CompilerPassInterface::class, $p);

        $def = $c->getDefinition('innmind_neo4j.translator.result');
        $arg = $def->getArgument(0);

        $this->assertSame(2, count($arg));
        $this->assertSame(
            [Aggregate::class, Relationship::class],
            array_keys($arg)
        );
        $this->assertInstanceOf(Reference::class, $arg[Aggregate::class]);
        $this->assertInstanceOf(Reference::class, $arg[Relationship::class]);
        $this->assertSame(
            'innmind_neo4j.translator.result.aggregate',
            (string) $arg[Aggregate::class]
        );
        $this->assertSame(
            'innmind_neo4j.translator.result.relationship',
            (string) $arg[Relationship::class]
        );
    }

    public function testProcessTranslatorIdentityMatch()
    {
        $c = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $c);
        $this->assertSame(
            null,
            ($p = new RegisterTagMapPass(
                'innmind_neo4j.translator.identity_match',
                'innmind_neo4j.translation.identity_match'
            ))->process($c)
        );
        $this->assertInstanceOf(CompilerPassInterface::class, $p);

        $def = $c->getDefinition('innmind_neo4j.translator.identity_match');
        $arg = $def->getArgument(0);

        $this->assertSame(2, count($arg));
        $this->assertSame(
            [Aggregate::class, Relationship::class],
            array_keys($arg)
        );
        $this->assertInstanceOf(Reference::class, $arg[Aggregate::class]);
        $this->assertInstanceOf(Reference::class, $arg[Relationship::class]);
        $this->assertSame(
            'innmind_neo4j.translator.identity_match.aggregate',
            (string) $arg[Aggregate::class]
        );
        $this->assertSame(
            'innmind_neo4j.translator.identity_match.relationship',
            (string) $arg[Relationship::class]
        );
    }

    public function testProcessTranslatorMatch()
    {
        $c = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $c);
        $this->assertSame(
            null,
            ($p = new RegisterTagMapPass(
                'innmind_neo4j.translator.match',
                'innmind_neo4j.translation.match'
            ))->process($c)
        );
        $this->assertInstanceOf(CompilerPassInterface::class, $p);

        $def = $c->getDefinition('innmind_neo4j.translator.match');
        $arg = $def->getArgument(0);

        $this->assertSame(2, count($arg));
        $this->assertSame(
            [Aggregate::class, Relationship::class],
            array_keys($arg)
        );
        $this->assertInstanceOf(Reference::class, $arg[Aggregate::class]);
        $this->assertInstanceOf(Reference::class, $arg[Relationship::class]);
        $this->assertSame(
            'innmind_neo4j.translator.match.aggregate',
            (string) $arg[Aggregate::class]
        );
        $this->assertSame(
            'innmind_neo4j.translator.match.relationship',
            (string) $arg[Relationship::class]
        );
    }
}
