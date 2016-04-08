<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Tests\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\{
    DependencyInjection\Compiler\InjectEntityDefinitionsPass,
    DependencyInjection\InnmindNeo4jExtension,
    Tests\Fixture\FooBundle\FooBundle,
    Tests\Fixture\BarBundle\BarBundle,
    Tests\Fixture\EmptyBundle\EmptyBundle
};
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Compiler\CompilerPassInterface
};

class InjectEntityDefinitionsPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $c = new ContainerBuilder;
        $c->setParameter(
            'kernel.bundles',
            [
                'FixtureFooBundle' => FooBundle::class,
                'FixtureBarBundle' => BarBundle::class,
                'FixtureEmptyBundle' => EmptyBundle::class,
            ]
        );
        (new InnmindNeo4jExtension)->load([], $c);
        $this->assertSame(
            null,
            ($p = new InjectEntityDefinitionsPass)->process($c)
        );
        $this->assertInstanceOf(CompilerPassInterface::class, $p);

        $def = $c->getDefinition('innmind_neo4j.metadata_builder');
        $calls = $def->getMethodCalls();

        $this->assertSame(1, count($calls));
        $this->assertSame('inject', $calls[0][0]);
        $this->assertSame(
            [
                [
                    'Foo' => [
                        'type' => 'aggregate',
                    ],
                ],
                [
                    'Bar' => [
                        'type' => 'relationship',
                    ],
                ],
            ],
            $calls[0][1][0]
        );
    }
}
