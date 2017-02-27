<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\Factory;

use Innmind\Neo4jBundle\Factory\MapFactory;
use Innmind\Neo4j\ONM\Translation\{
    ResultTranslator,
    EntityTranslatorInterface,
    IdentityMatchTranslator,
    IdentityMatchTranslatorInterface,
    MatchTranslator,
    MatchTranslatorInterface,
    SpecificationTranslator,
    SpecificationTranslatorInterface
};
use PHPUnit\Framework\TestCase;

class MapFactoryTest extends TestCase
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
                    'foo' => $this->createMock(EntityTranslatorInterface::class),
                ],
            ],
            [
                IdentityMatchTranslator::class,
                IdentityMatchTranslatorInterface::class,
                [
                    'foo' => $this->createMock(IdentityMatchTranslatorInterface::class),
                ],
            ],
            [
                MatchTranslator::class,
                MatchTranslatorInterface::class,
                [
                    'foo' => $this->createMock(MatchTranslatorInterface::class),
                ],
            ],
            [
                SpecificationTranslator::class,
                SpecificationTranslatorInterface::class,
                [
                    'foo' => $this->createMock(SpecificationTranslatorInterface::class),
                ],
            ],
        ];
    }
}
