<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Tests\Factory;

use Innmind\Neo4jBundle\Factory\ResultTranslatorFactory;
use Innmind\Neo4j\ONM\Translation\{
    ResultTranslator,
    EntityTranslatorInterface
};

class ResultTranslatorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        $r = ResultTranslatorFactory::make([
            'foo' => $this->getMock(EntityTranslatorInterface::class),
        ]);

        $this->assertInstanceOf(ResultTranslator::class, $r);
    }
}
