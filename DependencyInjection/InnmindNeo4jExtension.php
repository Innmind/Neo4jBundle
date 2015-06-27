<?php

namespace Innmind\Neo4jBundle\DependencyInjection;

use Innmind\Neo4j\ONM\EntityManagerFactory;
use Innmind\Neo4j\ONM\Configuration as Conf;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;

class InnmindNeo4jExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        foreach ($config['connections'] as $name => &$connection) {
            if (
                isset($connection['cluster']) &&
                empty($connection['cluster'])
            ) {
                unset($connection['cluster']);
            }
        }

        $defaultManager = null;

        foreach ($config['managers'] as $name => $manager) {
            if ($defaultManager === null) {
                $defaultManager = $name;
            }

            $configDef = new Definition(
                null,
                [
                    [
                        'reader' => $manager['reader'],
                        'cache' => sprintf(
                            '%%kernel.cache_dir%%/innmind/neo4j/%s',
                            $name
                        ),
                        'locations' => $manager['bundles'],
                    ],
                    new Parameter('kernel.debug'),
                ]
            );
            $configDef
                ->setFactoryClass(Conf::class)
                ->setFactoryMethod('create')
                ->setPublic(false);

            $def = new Definition(
                null,
                [
                    $config['connections'][$manager['connection']],
                    new Reference(sprintf(
                        'innmind_neo4j.config.%s',
                        $name
                    )),
                    new Reference('event_dispatcher'),
                ]
            );
            $def
                ->setFactoryClass(EntityManagerFactory::class)
                ->setFactoryMethod('make')
                ->addTag('innmind_neo4j.manager', ['alias' => $name]);

            $container->setDefinition(
                sprintf('innmind_neo4j.config.%s', $name),
                $configDef
            );
            $container->setDefinition(
                sprintf('innmind_neo4j.manager.%s', $name),
                $def
            );
        }

        $container
            ->getDefinition('innmind_neo4j.registry')
            ->addMethodCall('setDefaultManager', [$defaultManager]);
    }
}
