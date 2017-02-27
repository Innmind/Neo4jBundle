<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle;

use Innmind\Neo4jBundle\{
    InnmindNeo4jBundle,
    DependencyInjection\InnmindNeo4jExtension
};
use Fixtures\Innmind\Neo4jBundle\{
    FooBundle\FooBundle,
    BarBundle\BarBundle,
    EmptyBundle\EmptyBundle
};
use Innmind\Neo4j\ONM\ManagerInterface;
use Symfony\Component\{
    DependencyInjection\ContainerBuilder,
    DependencyInjection\Definition,
    HttpKernel\Bundle\Bundle
};
use Psr\Log\NullLogger;
use PHPUnit\Framework\TestCase;

class InnmindNeo4jBundleTest extends TestCase
{
    public function testBuild()
    {
        $container = new ContainerBuilder;
        ($bundle = new InnmindNeo4jBundle)->build($container);
        $this->assertInstanceOf(Bundle::class, $bundle);
        $container->registerExtension(new InnmindNeo4jExtension);
        $container->loadFromExtension('innmind_neo4j', []);
        $container->setDefinition(
            'logger',
            new Definition(NullLogger::class)
        );
        $container->setParameter(
            'kernel.bundles',
            [
                'FixtureFooBundle' => FooBundle::class,
                'FixtureBarBundle' => BarBundle::class,
                'FixtureEmptyBundle' => EmptyBundle::class,
            ]
        );
        $container->compile();

        $this->assertInstanceOf(
            ManagerInterface::class,
            $container->get('innmind_neo4j.manager')
        );
    }
}
