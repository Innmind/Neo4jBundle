<?php

namespace Innmind\Neo4jBundle\Tests\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\DependencyInjection\Compiler\RegisterManagersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterManagersPassTest extends \PHPUnit_Framework_TestCase
{
    protected $c;
    protected $p;

    public function setUp()
    {
        $this->c = new ContainerBuilder;
        $this->c->setDefinition(
            'foo',
            (new Definition)
                ->addTag('innmind_neo4j.manager', ['alias' => 'foo'])
        );
        $this->c->setDefinition(
            'innmind_neo4j.registry',
            new Definition
        );
        $this->p = new RegisterManagersPass;
    }

    public function testRegisterManager()
    {
        $this->p->process($this->c);
        $calls = $this->c
            ->getDefinition('innmind_neo4j.registry')
            ->getMethodCalls();

        $this->assertSame(1, count($calls));
        $this->assertSame(
            'addManager',
            $calls[0][0]
        );
        $this->assertSame(
            'foo',
            $calls[0][1][0]
        );
        $this->assertInstanceOf(
            Reference::class,
            $calls[0][1][1]
        );
        $this->assertSame(
            'foo',
            (string) $calls[0][1][1]
        );
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage You must define an alias for the manager "bar"
     */
    public function testThrowIfNoAliasSet()
    {
        $this->c->setDefinition(
            'bar',
            (new Definition)
                ->addTag('innmind_neo4j.manager')
        );
        $this->p->process($this->c);
    }
}
