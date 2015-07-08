<?php

namespace Innmind\Neo4jBundle\Tests;

use Innmind\Neo4jBundle\Generator;
use Innmind\Neo4jBundle\Generator\EntityGenerator;
use Innmind\Neo4jBundle\Generator\RepositoryGenerator;
use Innmind\Neo4jBundle\FileDumper;
use Innmind\Neo4j\ONM\Mapping\NodeMetadata;
use Innmind\Neo4j\ONM\Mapping\Property;
use Innmind\Neo4j\ONM\IdentityMap;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $g;

    public function setUp()
    {
        $this->g = new Generator(
            new EntityGenerator,
            new RepositoryGenerator,
            new FileDumper
        );
    }

    public function testGenerate()
    {
        $meta = new NodeMetadata;
        $meta
            ->setClass('Innmind\Entity\Resource')
            ->setRepositoryClass('Innmind\Entity\ResourceRepository')
            ->addProperty(
                (new Property)
                    ->setName('uri')
                    ->setType('string')
            )
            ->addProperty(
                (new Property)
                    ->setName('created')
                    ->setType('date')
            )
            ->addProperty(
                (new Property)
                    ->setName('referrer')
                    ->setType('relationship')
                    ->addOption('relationship', 'Referrer')
            );
        $map = new IdentityMap;
        $map->addClass('Referrer');

        $this->assertSame(
            $this->g,
            $this->g->generate($meta, $map)
        );
        $this->assertTrue(file_exists('src/Innmind/Entity/Resource.php'));
        $this->assertTrue(file_exists('src/Innmind/Entity/ResourceRepository.php'));

        unlink('src/Innmind/Entity/Resource.php');
        unlink('src/Innmind/Entity/ResourceRepository.php');
        rmdir('src/Innmind/Entity');
        rmdir('src/Innmind');
        rmdir('src');
    }
}
