<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\DependencyInjection;

use Innmind\Neo4j\DBAL\{
    Server,
    Authentication
};
use Symfony\Component\{
    DependencyInjection\ContainerBuilder,
    Config\FileLocator,
    HttpKernel\DependencyInjection\Extension,
    DependencyInjection\Loader,
    DependencyInjection\Reference
};

class InnmindNeo4jExtension extends Extension
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
        $transactions = $container->getDefinition('innmind_neo4j.connection.transactions');
        $transport = $container->getDefinition('innmind_neo4j.connection.transport');
        $server = new Server(
            $config['scheme'],
            $config['host'],
            $config['port']
        );
        $auth = new Authentication(
            $config['user'],
            $config['password']
        );

        $transactions
            ->replaceArgument(0, $server)
            ->replaceArgument(1, $auth)
            ->replaceArgument(2, $config['timeout']);
        $transport
            ->replaceArgument(2, $server)
            ->replaceArgument(3, $auth)
            ->replaceArgument(4, $config['timeout']);

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
            $definition->addMethodCall('register', [$class]);
        }

        return $this;
    }
}
