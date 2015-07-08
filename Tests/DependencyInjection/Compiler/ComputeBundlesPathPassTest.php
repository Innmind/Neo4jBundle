<?php

namespace Innmind\Neo4jBundle\Tests\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\DependencyInjection\Compiler\ComputeBundlesPathPass;
use Innmind\Neo4jBundle\Fixtures\FooBundle\FooBundle;
use Innmind\Neo4jBundle\Fixtures\BarBundle\BarBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ComputeBundlesPathPassTest extends \PHPUnit_Framework_TestCase
{
    protected $c;
    protected $p;

    public function setUp()
    {
        $this->c = new ContainerBuilder;
        $this->c->setDefinition(
            'foo',
            (new Definition)
                ->addTag('innmind_neo4j.config')
                ->setPublic(false)
                ->addArgument([
                    'reader' => 'yaml',
                    'cache' => sys_get_temp_dir(),
                    'locations' => [
                        'FooBundle',
                    ],
                ])
        );
        $this->c->setParameter(
            'kernel.bundles',
            [
                'FooBundle' => FooBundle::class,
                'BarBundle' => BarBundle::class,
            ]
        );
        $this->p = new ComputeBundlesPathPass;
    }

    public function testProcessConfig()
    {
        $this->p->process($this->c);

        $def = $this->c->getDefinition('foo');
        $args = $def->getArguments()[0];

        $this->assertSame(
            [sprintf(
                '%s/Fixtures/FooBundle/Resources/config/neo4j.yml',
                getcwd()
            )],
            $args['locations']
        );
    }

    public function testRegisterAllBundlesWhenNoneSpecified()
    {
        $def = $this->c->getDefinition('foo');
        $args = $def->getArguments()[0];
        $args['locations'] = [];
        $def->replaceArgument(0, $args);

        $this->p->process($this->c);

        $args = $def->getArguments()[0];

        $this->assertSame(
            [
                sprintf(
                    '%s/Fixtures/FooBundle/Resources/config/neo4j.yml',
                    getcwd()
                ),
                sprintf(
                    '%s/Fixtures/BarBundle/Resources/config/neo4j',
                    getcwd()
                ),
            ],
            $args['locations']
        );
    }
}
