<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Tests\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\DependencyInjection\{
    Compiler\RegisterEntityTranslatorsPass,
    InnmindNeo4jExtension
};
use Innmind\Neo4j\ONM\Metadata\{
    Aggregate,
    Relationship
};
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Reference
};

class RegisterEntityTranslatorsPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $c = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $c);
        (new RegisterEntityTranslatorsPass)->process($c);

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
}
