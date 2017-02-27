<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\DependencyInjection;

use Symfony\Component\{
    DependencyInjection\ContainerBuilder,
    Config\FileLocator,
    HttpKernel\DependencyInjection\Extension,
    DependencyInjection\Loader,
    DependencyInjection\Reference
};

final class InnmindNeo4jExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');

        $this
            ->injectPersister(
                $container,
                $config['persister']
            )
            ->configureConnection(
                $container,
                $config['connection']
            )
            ->registerTypes(
                $container,
                $config['types']
            )
            ->injectMetadataConfiguration(
                $container,
                $config['metadata_configuration']
            )
            ->injectClock(
                $container,
                $config['clock']
            );
    }

    /**
     * Inject the defined service in the unit of work
     *
     * @param ContainerBuilder $container
     * @param string $service
     *
     * @return self
     */
    private function injectPersister(
        ContainerBuilder $container,
        string $service
    ): self {
        $container
            ->getDefinition('innmind_neo4j.unit_of_work')
            ->replaceArgument(5, new Reference($service));

        return $this;
    }

    /**
     * Inject value objects to make the connection work
     *
     * @param ContainerBuilder $container
     * @param array $config
     *
     * @return self
     */
    private function configureConnection(
        ContainerBuilder $container,
        array $config
    ): self {
        $transactions = $container->getDefinition('innmind_neo4j.dbal.connection.transactions');
        $transport = $container->getDefinition('innmind_neo4j.dbal.connection.transport');
        $server = $container->getDefinition('innmind_neo4j.dbal.connection.server');
        $authentication = $container->getDefinition('innmind_neo4j.dbal.connection.authentication');

        $server
            ->replaceArgument(0, $config['scheme'])
            ->replaceArgument(1, $config['host'])
            ->replaceArgument(2, $config['port']);
        $authentication
            ->replaceArgument(0, $config['user'])
            ->replaceArgument(1, $config['password']);

        return $this;
    }

    /**
     * Register the classes as added property types
     *
     * @param ContainerBuilder $container
     * @param array $types
     *
     * @return self
     */
    private function registerTypes(
        ContainerBuilder $container,
        array $types
    ): self {
        $definition = $container->getDefinition('innmind_neo4j.types');

        foreach ($types as $class) {
            $definition->addArgument($class);
        }

        return $this;
    }

    /**
     * Inject the configuration object into the metadata builder
     *
     * @param ContainerBuilder $container
     * @param string $config
     *
     * @return self
     */
    private function injectMetadataConfiguration(
        ContainerBuilder $container,
        string $config
    ): self {
        $container
            ->getDefinition('innmind_neo4j.metadata_builder')
            ->replaceArgument(2, new Reference($config));

        return $this;
    }

    /**
     * Inject the clock in the transactions service
     *
     * @param ContainerBuilder $container
     * @param string $clock
     *
     * @return self
     */
    private function injectClock(
        ContainerBuilder $container,
        string $clock
    ): self {
        $container
            ->getDefinition('innmind_neo4j.dbal.connection.transactions')
            ->replaceArgument(1, new Reference($clock));

        return $this;
    }
}
