<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\Factory;

use Innmind\Neo4jBundle\Factory\MapFactory;
use Innmind\Neo4j\ONM\Translation\{
    ResultTranslator,
    EntityTranslator,
    IdentityMatch\DelegationTranslator as IdentityMatchTranslator,
    IdentityMatchTranslator as IdentityMatchTranslatorInterface,
    Match\DelegationTranslator as MatchTranslator,
    MatchTranslator as MatchTranslatorInterface,
    Specification\DelegationTranslator as SpecificationTranslator,
    SpecificationTranslator as SpecificationTranslatorInterface
};
use PHPUnit\Framework\TestCase;

class MapFactoryTest extends TestCase
{
    /**
     * @dataProvider factories
     */
    public function testMake(string $class, string $type, array $argument)
    {
        $service = MapFactory::make($class, $type, $argument);

        $this->assertInstanceOf($class, $service);
    }

    public function factories()
    {
        return [
            [
                ResultTranslator::class,
                EntityTranslator::class,
                [
                    'foo' => $this->createMock(EntityTranslator::class),
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
