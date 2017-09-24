<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\Configurator;

use Innmind\Neo4jBundle\Configurator\RepositoryFactoryConfigurator;
use Innmind\Neo4j\ONM\{
    Metadata\ClassName,
    Metadata\Alias,
    Metadata\Entity,
    Metadatas,
    Repository,
    RepositoryFactory,
    UnitOfWork,
    Translation\Match\DelegationTranslator as MatchTranslator,
    Translation\IdentityMatch\DelegationTranslator as IdentityMatchTranslator,
    Translation\Specification\DelegationTranslator as SpecificationTranslator,
    Translation\ResultTranslator,
    Entity\Container,
    EntityFactory\EntityFactory,
    EntityFactory\Resolver,
    Identity\Generators,
    Persister
};
use Innmind\Neo4j\DBAL\Connection;
use PHPUnit\Framework\TestCase;

class RepositoryFactoryConfiguratorTest extends TestCase
{
    public function testConfigure()
    {
        $entity = $this->createMock(Entity::class);
        $entity
            ->method('class')
            ->willReturn(new ClassName('foo'));
        $entity
            ->method('alias')
            ->willReturn(new Alias('foo'));
        $metadatas = new Metadatas($entity);
        $configurator = new RepositoryFactoryConfigurator($metadatas);
        $configurator->register('foo', $repository = $this->createMock(Repository::class));
        $factory = new RepositoryFactory(
            new UnitOfWork(
                $this->createMock(Connection::class),
                $container = new Container,
                new EntityFactory(
                    new ResultTranslator,
                    $generators = new Generators,
                    new Resolver,
                    $container
                ),
                new IdentityMatchTranslator,
                $metadatas,
                $this->createMock(Persister::class),
                $generators
            ),
            new MatchTranslator,
            new SpecificationTranslator
        );

        $factory2 = $configurator->configure($factory);

        $this->assertSame($factory, $factory2);
        $this->assertSame($repository, $factory->make($entity));
    }
}
