<?php

namespace Innmind\Neo4jBundle;

use Innmind\Neo4jBundle\Generator\EntityGenerator;
use Innmind\Neo4jBundle\Generator\RepositoryGenerator;
use Innmind\Neo4j\ONM\Mapping\Metadata;
use Innmind\Neo4j\ONM\IdentityMap;

class Generator
{
    protected $entityGenerator;
    protected $repositoryGenerator;
    protected $fileDumper;

    public function __construct(
        EntityGenerator $entityGenerator,
        RepositoryGenerator $repositoryGenerator,
        FileDumper $fileDumper
    ) {
        $this->entityGenerator = $entityGenerator;
        $this->repositoryGenerator = $repositoryGenerator;
        $this->fileDumper = $fileDumper;
    }

    /**
     * Generate an entity class off of its metadata
     *
     * @param Metadata $meta
     *
     * @return Generator self
     */
    public function generate(Metadata $meta, IdentityMap $map)
    {
        $file = $this->entityGenerator->generate($meta, $map);
        $this->fileDumper->dump($meta->getClass(), $file);

        $class = $meta->getRepositoryClass();

        if ($class !== 'Innmind\Neo4j\ONM\Repository') {
            $file = $this->repositoryGenerator->generate($class);
            $this->fileDumper->dump($class, $file);
        }

        return $this;
    }
}
