<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\Factory;

use Innmind\Neo4jBundle\Factory\DataExtractorFactory;
use Innmind\Neo4j\ONM\{
    Metadatas,
    Metadata\EntityInterface,
    Metadata\Alias,
    Metadata\ClassName,
    Entity\DataExtractorInterface,
    Entity\DataExtractor
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class DataExtractorFactoryTest extends TestCase
{
    public function testMake()
    {
        $meta = $this->createMock(EntityInterface::class);
        $meta
            ->method('alias')
            ->willReturn(new Alias('stdClass'));
        $meta
            ->method('class')
            ->willReturn(new ClassName('stdClass'));
        $metadatas = new Metadatas($meta);

        $entity = new \stdClass;
        $mock = $this->createMock(DataExtractorInterface::class);
        $mock
            ->expects($this->once())
            ->method('extract')
            ->with($entity, $meta)
            ->willReturn(
                $expected = $this->createMock(MapInterface::class)
            );
        $extractor = DataExtractorFactory::make(
            $metadatas,
            [
                get_class($meta) => $mock,
            ]
        );

        $this->assertInstanceOf(DataExtractor::class, $extractor);
        $this->assertSame($expected, $extractor->extract(new \stdClass));
    }
}
