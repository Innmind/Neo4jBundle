<?php

namespace Innmind\Neo4jBundle\Generator;

use Memio\Model\Object;
use Memio\Model\File;
use Memio\Model\FullyQualifiedName;

class RepositoryGenerator
{
    /**
     * Generate the code to create a repository class for the given metadata
     *
     * @param string $class
     *
     * @return File
     */
    public function generate($class)
    {
        $parent = new Object('Innmind\Neo4j\ONM\Repository');
        $object = Object::make((string) $class)
            ->extend($parent);

        return File::make(null)
            ->setStructure($object)
            ->addFullyQualifiedName(new FullyQualifiedName(
                $parent->getFullyQualifiedName()
            ));
    }
}
