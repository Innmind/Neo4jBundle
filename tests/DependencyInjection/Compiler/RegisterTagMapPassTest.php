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
use PHPUnit\Framework\TestCase;

class RegisterTagMapPassTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CompilerPassInterface::class,
            new RegisterTagMapPass('service', 'tag')
        );
    }

    /**
     * @dataProvider tags
     */
    public function testProcess(
        string $service,
        string $tag,
        string $aggregate,
        string $relationship
    ) {
        $container = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $container);
        $this->assertNull(
            (new RegisterTagMapPass($service, $tag))->process($container)
        );

        $def = $container->getDefinition($service);
        $arg = $def->getArgument(2);

        $this->assertCount(2, $arg);
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
