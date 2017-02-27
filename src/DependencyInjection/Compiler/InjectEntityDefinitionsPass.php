<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\Exception\NoEntityDefinitionFoundException;
use Innmind\Filesystem\{
    Adapter\FilesystemAdapter,
    Exception\FileNotFoundException,
    DirectoryInterface
};
use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder
};
use Symfony\Component\Yaml\Yaml;

final class InjectEntityDefinitionsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        $configs = [];

        foreach ($bundles as $bundle => $class) {
            try {
                $configs[] = $this->computeConfig($class);
            } catch (NoEntityDefinitionFoundException $e) {
                //pass
            }
        }

        $container
            ->getDefinition('innmind_neo4j.metadata_builder')
            ->addMethodCall(
                'inject',
                [$configs]
            );
    }

    /**
     * Load the entity definitions for the given bundle
     *
     * @param string $class Bundle class FQCN
     *
     * @throws NoEntityDefinitionFoundException
     *
     * @return array
     */
    private function computeConfig(string $class): array
    {
        try {
            $refl = new \ReflectionClass($class);
            $dir = (new FilesystemAdapter(dirname($refl->getFileName())))
                ->get('Resources')
                ->get('config');

            if ($dir->has('neo4j.yml')) {
                return Yaml::parse((string) $dir->get('neo4j.yml')->content());
            }

            $dir = $dir->get('neo4j');
            $config = [];

            foreach ($dir as $file) {
                if ($file instanceof DirectoryInterface) {
                    continue;
                }

                $config = array_merge(
                    $config,
                    Yaml::parse((string) $file->content())
                );
            }

            return $config;
        } catch (FileNotFoundException $e) {
            throw new NoEntityDefinitionFoundException;
        }
    }
}
