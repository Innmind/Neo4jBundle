<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Tests\Factory;

use Innmind\Neo4jBundle\Factory\MapFactory;
use Innmind\Neo4j\ONM\Translation\{
    ResultTranslator,
    EntityTranslatorInterface,
    IdentityMatchTranslator,
    IdentityMatchTranslatorInterface
};

class MapFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMakeResultTranslator()
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

    public function testMakeIdentityMatchTranslator()
    {
        $r = MapFactory::make(
            IdentityMatchTranslator::class,
            IdentityMatchTranslatorInterface::class,
            [
                'foo' => $this->getMock(IdentityMatchTranslatorInterface::class),
            ]
        );

        $this->assertInstanceOf(IdentityMatchTranslator::class, $r);
    }
}
