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
    HttpKernel\Bundle\Bundle,
    EventDispatcher\EventDispatcher
};
use Psr\Log\NullLogger;
use PHPUnit\Framework\TestCase;

class InnmindNeo4jBundleTest extends TestCase
{
    public function testBuild()
    {
        $c = new ContainerBuilder;
        ($b = new InnmindNeo4jBundle)->build($c);
        $this->assertInstanceOf(Bundle::class, $b);
        $c->registerExtension(new InnmindNeo4jExtension);
        $c->loadFromExtension('innmind_neo4j', []);
        $c->setDefinition(
            'event_dispatcher',
            new Definition(EventDispatcher::class)
        );
        $c->setDefinition(
            'logger',
            new Definition(NullLogger::class)
        );
        $c->setParameter(
            'kernel.bundles',
            [
                'FixtureFooBundle' => FooBundle::class,
                'FixtureBarBundle' => BarBundle::class,
                'FixtureEmptyBundle' => EmptyBundle::class,
            ]
        );
        $c->compile();

        $this->assertInstanceOf(
            ManagerInterface::class,
            $c->get('innmind_neo4j.manager')
        );
    }
}
