<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\{
    DependencyInjection\Compiler\InjectEntityDefinitionsPass,
    DependencyInjection\InnmindNeo4jExtension
};
use Fixtures\Innmind\Neo4jBundle\{
    FooBundle\FooBundle,
    BarBundle\BarBundle,
    EmptyBundle\EmptyBundle
};
use Innmind\Neo4j\ONM\Identity\Uuid;
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Compiler\CompilerPassInterface
};
use PHPUnit\Framework\TestCase;

class InjectEntityDefinitionsPassTest extends TestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder;
        $container->setParameter(
            'kernel.bundles',
            [
                'FixtureFooBundle' => FooBundle::class,
                'FixtureBarBundle' => BarBundle::class,
                'FixtureEmptyBundle' => EmptyBundle::class,
            ]
        );
        (new InnmindNeo4jExtension)->load([], $container);
        $this->assertNull(
            ($pass = new InjectEntityDefinitionsPass)->process($container)
        );
        $this->assertInstanceOf(CompilerPassInterface::class, $pass);

        $def = $container->getDefinition('innmind_neo4j.metadata_builder');
        $calls = $def->getMethodCalls();

        $this->assertCount(1, $calls);
        $this->assertSame('inject', $calls[0][0]);
        $this->assertSame(
            [
                [
                    'Foo' => [
                        'type' => 'aggregate',
                        'identity' => [
                            'property' => 'uuid',
                            'type' => Uuid::class,
                        ],
                    ],
                ],
                [
                    'Bar' => [
                        'type' => 'relationship',
                        'identity' => [
                            'property' => 'uuid',
                            'type' => Uuid::class,
                        ],
                        'rel_type' => 'BAR',
                        'startNode' => [
                            'property' => 'start',
                            'type' => Uuid::class,
                            'target' => 'uuid',
                        ],
                        'endNode' => [
                            'property' => 'end',
                            'type' => Uuid::class,
                            'target' => 'uuid',
                        ],
                    ],
                ],
            ],
            $calls[0][1][0]
        );
    }
}
