<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Configurator;

use Innmind\Neo4j\ONM\{
    Metadatas,
    RepositoryInterface,
    RepositoryFactory
};
use Innmind\Immutable\Map;

final class RepositoryFactoryConfigurator
{
    private $metadatas;
    private $repositories;

    public function __construct(Metadatas $metadatas)
    {
        $this->metadatas = $metadatas;
        $this->repositories = new Map('string', RepositoryInterface::class);
    }

    /**
     * Register a newrepository for the given entity class
     *
     * @param string $class
     * @param RepositoryInterface $repository
     *
     * @return self
     */
    public function register(string $class, RepositoryInterface $repository): self
    {
        $this->repositories = $this->repositories->put($class, $repository);

        return $this;
    }

    /**
     * Inject registered repositories into the given repository factory
     *
     * @param RepositoryFactory $factory
     *
     * @return RepositoryFactory
     */
    public function configure(RepositoryFactory $factory): RepositoryFactory
    {
        $this
            ->repositories
            ->foreach(function(
                string $class,
                RepositoryInterface $repository
            ) use (
                $factory
            ) {
                $factory->register(
                    $this->metadatas->get($class),
                    $repository
                );
            });

        return $factory;
    }
}
