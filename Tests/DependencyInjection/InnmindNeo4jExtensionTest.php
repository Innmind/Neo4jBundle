<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Tests\DependencyInjection;

use Innmind\Neo4jBundle\DependencyInjection\InnmindNeo4jExtension;
use Innmind\Neo4j\DBAL\{
    Server,
    Authentication
};
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Reference
};

class InnmindNeo4jExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $c;
    private $e;

    public function setUp()
    {
        $this->c = new ContainerBuilder;
        $this->e = new InnmindNeo4jExtension;
        $config = [
            'innmind_neo4j' => [
                'connection' => [
                    'scheme' => 'http',
                    'host' => 'docker',
                    'port' => 1337,
                    'timeout' => 42,
                    'user' => 'neo4j',
                    'password' => 'ci',
                ],
                'types' => ['foo', 'bar'],
                'persister' => 'another_service',
                'metadata_configuration' => 'config',
            ],
        ];

        $this->e->load($config, $this->c);
    }

    public function testPersister()
    {
        $def = $this->c->getDefinition('innmind_neo4j.unit_of_work');

        $this->assertInstanceOf(Reference::class, $def->getArgument(5));
        $this->assertSame('another_service', (string) $def->getArgument(5));
    }

    public function testConnection()
    {
        $def = $this->c->getDefinition('innmind_neo4j.connection.server');
        $this->assertSame('http', $def->getArgument(0));
        $this->assertSame('docker', $def->getArgument(1));
        $this->assertSame(1337, $def->getArgument(2));

        $def = $this->c->getDefinition('innmind_neo4j.connection.authentication');
        $this->assertSame('neo4j', $def->getArgument(0));
        $this->assertSame('ci', $def->getArgument(1));

        $def = $this->c->getDefinition('innmind_neo4j.connection.transactions');
        $this->assertSame(42, $def->getArgument(2));

        $transport = $this->c->getDefinition('innmind_neo4j.connection.transport');
        $this->assertSame(42, $transport->getArgument(4));
    }

    public function testTypes()
    {
        $def = $this->c->getDefinition('innmind_neo4j.types');
        $calls = $def->getMethodCalls();

        $this->assertSame(2, count($calls));
        $this->assertSame('register', $calls[0][0]);
        $this->assertSame('foo', $calls[0][1][0]);
        $this->assertSame('register', $calls[1][0]);
        $this->assertSame('bar', $calls[1][1][0]);
    }

    public function testMetadataConfiguration()
    {
        $def = $this->c->getDefinition('innmind_neo4j.metadata_builder');

        $this->assertInstanceOf(Reference::class, $def->getArgument(2));
        $this->assertSame('config', (string) $def->getArgument(2));
    }

    public function testDefaultPersister()
    {
        $c = new ContainerBuilder;
        $this->e->load([], $c);
        $def = $c->getDefinition('innmind_neo4j.unit_of_work');

        $this->assertInstanceOf(Reference::class, $def->getArgument(5));
        $this->assertSame(
            'innmind_neo4j.persister.delegation',
            (string) $def->getArgument(5)
        );
    }

    public function testDefaultConnection()
    {
        $c = new ContainerBuilder;
        $this->e->load([], $c);

        $def = $this->c->getDefinition('innmind_neo4j.connection.server');
        $this->assertSame('http', $def->getArgument(0));
        $this->assertSame('docker', $def->getArgument(1));
        $this->assertSame(1337, $def->getArgument(2));

        $def = $this->c->getDefinition('innmind_neo4j.connection.authentication');
        $this->assertSame('neo4j', $def->getArgument(0));
        $this->assertSame('ci', $def->getArgument(1));

        $def = $c->getDefinition('innmind_neo4j.connection.transactions');
        $this->assertSame(60, $def->getArgument(2));

        $transport = $c->getDefinition('innmind_neo4j.connection.transport');
        $this->assertSame(60, $transport->getArgument(4));
    }

    public function testDefaultTypes()
    {
        $c = new ContainerBuilder;
        $this->e->load([], $c);
        $def = $c->getDefinition('innmind_neo4j.types');
        $calls = $def->getMethodCalls();

        $this->assertSame(0, count($calls));
    }

    public function testDefaultMetadataConfiguration()
    {
        $c = new ContainerBuilder;
        $this->e->load([], $c);
        $def = $c->getDefinition('innmind_neo4j.metadata_builder');

        $this->assertInstanceOf(Reference::class, $def->getArgument(2));
        $this->assertSame('innmind_neo4j.metadata_builder.configuration', (string) $def->getArgument(2));
    }
}
