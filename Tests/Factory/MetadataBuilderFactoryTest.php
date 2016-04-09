<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Tests\Factory;

use Innmind\Neo4jBundle\Factory\MetadataBuilderFactory;
use Innmind\Neo4j\ONM\{
    Types,
    Metadata\Aggregate,
    MetadataFactory\AggregateFactory,
    Configuration,
    MetadataBuilder
};

class MetadataBuilderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        $mb = MetadataBuilderFactory::make(
            new Types,
            [
                Aggregate::class => new AggregateFactory(new Types),
            ],
            new Configuration
        );

        $this->assertInstanceOf(MetadataBuilder::class, $mb);
    }
}
