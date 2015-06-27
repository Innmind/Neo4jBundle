<?php

namespace Innmind\Neo4jBundle\Tests;

use Innmind\Neo4jBundle\FileDumper;
use Innmind\Neo4jBundle\Generator\EntityGenerator;
use Innmind\Neo4j\ONM\Mapping\NodeMetadata;
use Innmind\Neo4j\ONM\Mapping\Property;
use Innmind\Neo4j\ONM\IdentityMap;
use Memio\Model\File;

class FileDumperTest extends \PHPUnit_Framework_TestCase
{
    protected $fd;

    public function setUp()
    {
        $this->fd = new FileDumper;
    }

    public function testDump()
    {
        $meta = new NodeMetadata;
        $meta
            ->setClass('Innmind\Entity\Resource')
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
        $file = (new EntityGenerator)->generate($meta, $map);

        $this->assertSame(
            $this->fd,
            $this->fd->dump($meta->getClass(), $file)
        );
        $path = 'src/Innmind/Entity/Resource.php';
        $this->assertTrue(file_exists($path));

        $this->fd->dump($meta->getClass(), $file);
        $this->assertTrue(file_exists($path . '~'));

        unlink($path);
        unlink($path . '~');
        rmdir('src/Innmind/Entity');
        rmdir('src/Innmind');
        rmdir('src');
    }
}
