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
    }
}
