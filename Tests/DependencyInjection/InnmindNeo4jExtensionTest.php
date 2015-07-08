<?php

namespace Innmind\Neo4jBundle\Tests\DependencyInjection;

use Innmind\Neo4jBundle\DependencyInjection\InnmindNeo4jExtension;
use Innmind\Neo4jBundle\DependencyInjection\Configuration;
use Innmind\Neo4j\ONM\EntityManagerFactory;
use Innmind\Neo4j\ONM\Configuration as Conf;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InnmindNeo4jExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;
    protected $container;
    protected $config;

    public function setUp()
    {
        $this->container = new ContainerBuilder;
        $this->extension = new InnmindNeo4jExtension;
        $this->config = [
            'innmind_neo4j' => [
                'connections' => [
                    'default' => [
                        'username' => 'neo4j',
                        'password' => 'neo4j',
                    ],
                    'cluster_conn' => [
                        'cluster' => [
                            'a' => [
                                'username' => 'foo',
                                'password' => 'foo',
                            ],
                        ],
                    ],
                ],
                'managers' => [
                    'default' => [
                        'reader' => 'yaml',
                        'connection' => 'default',
                    ],
                ],
            ],
        ];
        $this->extension->load($this->config, $this->container);
    }

    public function testManagersAreDefined()
    {
        $defs = $this->container->findTaggedServiceIds('innmind_neo4j.manager');

        $this->assertSame(1, count($defs));
    }

    public function testManagerDefinition()
    {
        $def = $this->container->getDefinition('innmind_neo4j.manager.default');

        $args = $def->getArguments();

        $this->assertSame(
            [
                'username' => 'neo4j',
                'password' => 'neo4j',
                'scheme' => 'http',
                'host' => 'localhost',
                'port' => 7474,
                'timeout' => 60,
            ],
            $args[0]
        );
        $this->assertSame(
            'innmind_neo4j.config.default',
            (string) $args[1]
        );
        $this->assertSame(
            'event_dispatcher',
            (string) $args[2]
        );
        $this->assertSame(
            EntityManagerFactory::class,
            $def->getFactory()[0]
        );
        $this->assertSame(
            'make',
            $def->getFactory()[1]
        );
        $this->assertSame(
            [
                'innmind_neo4j.manager' => [[
                    'alias' => 'default',
                ]],
            ],
            $def->getTags()
        );
    }

    public function testConfigDefinition()
    {
        $def = $this->container->getDefinition('innmind_neo4j.config.default');

        $args = $def->getArguments();

        $this->assertSame(
            [
                'reader' => 'yaml',
                'cache' => '%kernel.cache_dir%/innmind/neo4j/default',
                'locations' => [],
            ],
            $args[0]
        );
        $this->assertSame(
            'kernel.debug',
            (string) $args[1]
        );
        $this->assertFalse($def->isPublic());
        $this->assertSame(
            Conf::class,
            $def->getFactory()[0]
        );
        $this->assertSame(
            'create',
            $def->getFactory()[1]
        );
        $this->assertSame(
            [
                'innmind_neo4j.config' => [[]],
            ],
            $def->getTags()
        );
    }

    public function testRegistryDefintion()
    {
        $def = $this->container->getDefinition('innmind_neo4j.registry');

        $this->assertSame(
            [[
                'setDefaultManager', ['default']
            ]],
            $def->getMethodCalls()
        );
    }

    public function testAliasesAreSet()
    {
        $this->assertTrue($this->container->hasAlias('neo4j'));
        $this->assertTrue($this->container->hasAlias('graph'));
    }

    public function testAliasesAreNotSet()
    {
        $this->config['innmind_neo4j']['disable_aliases'] = true;
        $container = new ContainerBuilder;
        $this->extension->load($this->config, $container);

        $this->assertFalse($container->hasAlias('neo4j'));
        $this->assertFalse($container->hasAlias('graph'));

    }
}
