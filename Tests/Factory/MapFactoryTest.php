<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Tests\Factory;

use Innmind\Neo4jBundle\Factory\MapFactory;
use Innmind\Neo4j\ONM\Translation\{
    ResultTranslator,
    EntityTranslatorInterface,
    IdentityMatchTranslator,
    IdentityMatchTranslatorInterface,
    MatchTranslator,
    MatchTranslatorInterface
};

class MapFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider factories
     */
    public function testMake(string $class, string $type, array $argument)
    {
        $r = MapFactory::make($class, $type, $argument);

        $this->assertInstanceOf($class, $r);
    }

    public function factories()
    {
        return [
            [
                ResultTranslator::class,
                EntityTranslatorInterface::class,
                [
                    'foo' => $this->getMock(EntityTranslatorInterface::class),
                ],
            ],
            [
                IdentityMatchTranslator::class,
                IdentityMatchTranslatorInterface::class,
                [
                    'foo' => $this->getMock(IdentityMatchTranslatorInterface::class),
                ],
            ],
            [
                MatchTranslator::class,
                MatchTranslatorInterface::class,
                [
                    'foo' => $this->getMock(MatchTranslatorInterface::class),
                ],
            ],
        ];
    }
}
