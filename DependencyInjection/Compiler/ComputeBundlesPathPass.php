<?php

namespace Innmind\Neo4jBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Replace bundles names by the locations of the configs
 */
class ComputeBundlesPathPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $ids = $container->findTaggedServiceIds('innmind_neo4j.config');
        $bundles = $container->getParameter('kernel.bundles');

        foreach ($ids as $id => $tags) {
            $def = $container->getDefinition($id);
            $args = $def->getArguments();
            $args[0]['locations'] = $this->computeLocations(
                $bundles,
                $args[0]['locations']
            );
            $def->replaceArgument(0, $args[0]);
        }
    }

    /**
     * Replace the bundles' name by the appropriate path to the config location
     *
     * @param array $bundles
     * @param array $locations
     *
     * @return array
     */
    protected function computeLocations(
        array $bundles,
        array $locations
    ) {
        $paths = [];

        foreach ($bundles as $bundle => $className) {
            if (!empty($locations) && !in_array($bundle, $locations, true)) {
                continue;
            }

            $refl = new \ReflectionClass($className);
            $dir = dirname($refl->getFileName());
            $wishedFile = sprintf('%s/Resources/config/neo4j.yml', $dir);
            $wishedDir = sprintf('%s/Resources/config/neo4j', $dir);

            if (file_exists($wishedFile)) {
                $paths[] = $wishedFile;
            } else if (file_exists($wishedDir) && is_dir($wishedDir)) {
                $paths[] = $wishedDir;
            }
        }

        return $paths;
    }
}
