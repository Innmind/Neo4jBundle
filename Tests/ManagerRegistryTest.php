<?php

namespace Innmind\Neo4jBundle\Tests;

use Innmind\Neo4jBundle\ManagerRegistry;
use Innmind\Neo4j\ONM\EntityManager;

class ManagerRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $r;

    public function setUp()
    {
        $this->r = new ManagerRegistry;
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage No entity manager found with the name "foo"
     */
    public function testThrowIfUnknownManager()
    {
        $this->r->getManager('foo');
    }

    public function testGetDefaultManager()
    {
        $em = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->r->addManager('foo', $em);
        $this->r->addManager('default', $em);

        $this->assertSame(
            $em,
            $this->r->getManager()
        );

        $this->r->setDefaultManager('foo');

        $this->assertSame(
            $em,
            $this->r->getManager()
        );
    }

    public function testAddManager()
    {
        $em = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertSame(
            $this->r,
            $this->r->addManager('foo', $em)
        );
        $this->assertSame(
            $em,
            $this->r->getManager('foo')
        );
    }
}
