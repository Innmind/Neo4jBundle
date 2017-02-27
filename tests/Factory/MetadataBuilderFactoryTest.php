<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\Factory;

use Innmind\Neo4jBundle\Factory\MetadataBuilderFactory;
use Innmind\Neo4j\ONM\{
    Types,
    Metadata\Aggregate,
    MetadataFactory\AggregateFactory,
    Configuration,
    MetadataBuilder
};
use PHPUnit\Framework\TestCase;

class MetadataBuilderFactoryTest extends TestCase
{
    public function testMake()
    {
        $builder = MetadataBuilderFactory::make(
            new Types,
            [
                Aggregate::class => new AggregateFactory(new Types),
            ],
            new Configuration
        );

        $this->assertInstanceOf(MetadataBuilder::class, $builder);
    }
}
