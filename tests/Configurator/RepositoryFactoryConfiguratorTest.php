<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\Configurator;

use Innmind\Neo4jBundle\Configurator\RepositoryFactoryConfigurator;
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
use PHPUnit\Framework\TestCase;

class RepositoryFactoryConfiguratorTest extends TestCase
{
    public function testConfigure()
    {
        $entity = $this->createMock(EntityInterface::class);
        $entity
            ->method('class')
            ->willReturn(new ClassName('foo'));
        $entity
            ->method('alias')
            ->willReturn(new Alias('foo'));
        $m = new Metadatas;
        $m->register($entity);
        $c = new RepositoryFactoryConfigurator($m);
        $c->register('foo', $r = $this->createMock(RepositoryInterface::class));
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
