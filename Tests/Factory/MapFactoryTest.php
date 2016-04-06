<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Tests\Factory;

use Innmind\Neo4jBundle\Factory\MapFactory;
use Innmind\Neo4j\ONM\Translation\{
    ResultTranslator,
    EntityTranslatorInterface
};

class MapFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        $r = MapFactory::make(
            ResultTranslator::class,
            EntityTranslatorInterface::class,
            [
                'foo' => $this->getMock(EntityTranslatorInterface::class),
            ]
        );

        $this->assertInstanceOf(ResultTranslator::class, $r);
    }
}
