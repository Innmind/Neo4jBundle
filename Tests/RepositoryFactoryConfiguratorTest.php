<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Tests;

use Innmind\Neo4jBundle\RepositoryFactoryConfigurator;
use Innmind\Neo4j\ONM\{
    Metadata\ClassName,
    Metadata\Alias,
    Metadata\EntityInterface,
    Metadatas,
    RepositoryInterface,
    RepositoryFactory,
    UnitOfWork,
    Translation\MatchTranslator,
    Translation\SpecificationTranslator
};

class RepositoryFactoryConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigure()
    {
        $entity = $this->getMock(EntityInterface::class);
        $entity
            ->method('class')
            ->willReturn(new ClassName('foo'));
        $entity
            ->method('alias')
            ->willReturn(new Alias('foo'));
        $m = new Metadatas;
        $m->register($entity);
        $c = new RepositoryFactoryConfigurator($m);
        $c->register('foo', $r = $this->getMock(RepositoryInterface::class));
        $f = new RepositoryFactory(
            $this
                ->getMockBuilder(UnitOfWork::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this
                ->getMockBuilder(MatchTranslator::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this
                ->getMockBuilder(SpecificationTranslator::class)
                ->disableOriginalConstructor()
                ->getMock()
        );

        $f2 = $c->configure($f);

        $this->assertSame($f, $f2);
        $this->assertSame($r, $f->make($entity));
    }
}
