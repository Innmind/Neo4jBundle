<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\DependencyInjection\Compiler;

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
    /**
     * @dataProvider tags
     */
    public function testProcess(
        string $service,
        string $tag,
        string $aggregate,
        string $relationship
    ) {
        $c = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $c);
        $this->assertSame(
            null,
            ($p = new RegisterTagMapPass($service, $tag))->process($c)
        );
        $this->assertInstanceOf(CompilerPassInterface::class, $p);

        $def = $c->getDefinition($service);
        $arg = $def->getArgument(2);

        $this->assertSame(2, count($arg));
        $this->assertSame(
            [Aggregate::class, Relationship::class],
            array_keys($arg)
        );
        $this->assertInstanceOf(Reference::class, $arg[Aggregate::class]);
        $this->assertInstanceOf(Reference::class, $arg[Relationship::class]);
        $this->assertSame($aggregate, (string) $arg[Aggregate::class]);
        $this->assertSame($relationship, (string) $arg[Relationship::class]);
    }

    public function tags()
    {
        return [
            [
                'innmind_neo4j.translator.result',
                'innmind_neo4j.translation.result',
                'innmind_neo4j.translator.result.aggregate',
                'innmind_neo4j.translator.result.relationship',
            ],
            [
                'innmind_neo4j.translator.identity_match',
                'innmind_neo4j.translation.identity_match',
                'innmind_neo4j.translator.identity_match.aggregate',
                'innmind_neo4j.translator.identity_match.relationship',
            ],
            [
                'innmind_neo4j.translator.match',
                'innmind_neo4j.translation.match',
                'innmind_neo4j.translator.match.aggregate',
                'innmind_neo4j.translator.match.relationship',
            ],
            [
                'innmind_neo4j.translator.specification',
                'innmind_neo4j.translation.specification',
                'innmind_neo4j.translator.specification.aggregate',
                'innmind_neo4j.translator.specification.relationship',
            ],
        ];
    }
}
