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
use Innmind\Immutable\CollectionInterface;

class DataExtractorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        $metadatas = new Metadatas;
        $meta = $this->createMock(EntityInterface::class);
        $meta
            ->method('alias')
            ->willReturn(new Alias('stdClass'));
        $meta
            ->method('class')
            ->willReturn(new ClassName('stdClass'));
        $metadatas->register($meta);

        $entity = new \stdClass;
        $mock = $this->createMock(DataExtractorInterface::class);
        $mock
            ->expects($this->once())
            ->method('extract')
            ->with($entity, $meta)
            ->willReturn(
                $expected = $this->createMock(CollectionInterface::class)
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
