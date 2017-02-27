<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\DependencyInjection;

use Innmind\Neo4jBundle\DependencyInjection\InnmindNeo4jExtension;
use Innmind\Neo4j\DBAL\{
    Server,
    Authentication
};
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Reference
};
use PHPUnit\Framework\TestCase;

class InnmindNeo4jExtensionTest extends TestCase
{
    private $container;
    private $extension;

    public function setUp()
    {
        $this->container = new ContainerBuilder;
        $this->extension = new InnmindNeo4jExtension;
        $config = [
            'innmind_neo4j' => [
                'connection' => [
                    'scheme' => 'http',
                    'host' => 'docker',
                    'port' => 1337,
                    'user' => 'neo4j',
                    'password' => 'ci',
                ],
                'types' => ['foo', 'bar'],
                'persister' => 'another_service',
                'metadata_configuration' => 'config',
                'clock' => 'clock',
                'event_bus' => 'event_bus',
            ],
        ];

        $this->extension->load($config, $this->container);
    }

    public function testPersister()
    {
        $def = $this->container->getDefinition('innmind_neo4j.unit_of_work');

        $this->assertInstanceOf(Reference::class, $def->getArgument(5));
        $this->assertSame('another_service', (string) $def->getArgument(5));
    }

    public function testConnection()
    {
        $def = $this->container->getDefinition('innmind_neo4j.dbal.connection.server');
        $this->assertSame('http', $def->getArgument(0));
        $this->assertSame('docker', $def->getArgument(1));
        $this->assertSame(1337, $def->getArgument(2));

        $def = $this->container->getDefinition('innmind_neo4j.dbal.connection.authentication');
        $this->assertSame('neo4j', $def->getArgument(0));
        $this->assertSame('ci', $def->getArgument(1));
    }

    public function testTypes()
    {
        $def = $this->container->getDefinition('innmind_neo4j.types');
        $arguments = $def->getArguments();

        $this->assertCount(2, $arguments);
        $this->assertSame('foo', $arguments[0]);
        $this->assertSame('bar', $arguments[1]);
    }

    public function testMetadataConfiguration()
    {
        $def = $this->container->getDefinition('innmind_neo4j.metadata_builder');

        $this->assertInstanceOf(Reference::class, $def->getArgument(2));
        $this->assertSame('config', (string) $def->getArgument(2));
    }

    public function testClock()
    {
        $def = $this->container->getDefinition('innmind_neo4j.dbal.connection.transactions');

        $this->assertInstanceOf(Reference::class, $def->getArgument(1));
        $this->assertSame('clock', (string) $def->getArgument(1));
    }

    public function testEventBus()
    {
        $alias = $this->container->getAlias('innmind_neo4j.event_bus');
        $this->assertSame('event_bus', (string) $alias);
    }

    public function testDefaultPersister()
    {
        $container = new ContainerBuilder;
        $this->extension->load([], $container);
        $def = $container->getDefinition('innmind_neo4j.unit_of_work');

        $this->assertInstanceOf(Reference::class, $def->getArgument(5));
        $this->assertSame(
            'innmind_neo4j.persister.delegation',
            (string) $def->getArgument(5)
        );
    }

    public function testDefaultConnection()
    {
        $container = new ContainerBuilder;
        $this->extension->load([], $container);

        $def = $this->container->getDefinition('innmind_neo4j.dbal.connection.server');
        $this->assertSame('http', $def->getArgument(0));
        $this->assertSame('docker', $def->getArgument(1));
        $this->assertSame(1337, $def->getArgument(2));

        $def = $this->container->getDefinition('innmind_neo4j.dbal.connection.authentication');
        $this->assertSame('neo4j', $def->getArgument(0));
        $this->assertSame('ci', $def->getArgument(1));
    }

    public function testDefaultTypes()
    {
        $container = new ContainerBuilder;
        $this->extension->load([], $container);
        $def = $container->getDefinition('innmind_neo4j.types');
        $arguments = $def->getArguments();

        $this->assertCount(0, $arguments);
    }

    public function testDefaultClock()
    {
        $container = new ContainerBuilder;
        $this->extension->load([], $container);
        $def = $container->getDefinition('innmind_neo4j.dbal.connection.transactions');
        $this->assertInstanceOf(Reference::class, $def->getArgument(1));
        $this->assertSame('innmind_neo4j.clock', (string) $def->getArgument(1));
    }

    public function testDefaultMetadataConfiguration()
    {
        $container = new ContainerBuilder;
        $this->extension->load([], $container);
        $def = $container->getDefinition('innmind_neo4j.metadata_builder');

        $this->assertInstanceOf(Reference::class, $def->getArgument(2));
        $this->assertSame('innmind_neo4j.metadata_builder.configuration', (string) $def->getArgument(2));
    }

    public function testDefaultEventBus()
    {
        $container = new ContainerBuilder;
        $this->extension->load([], $container);
        $alias = $container->getAlias('innmind_neo4j.event_bus');
        $this->assertSame('innmind_neo4j.event_bus.null', (string) $alias);
    }
}
