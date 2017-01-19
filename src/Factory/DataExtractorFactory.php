<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Factory;

use Innmind\Neo4j\ONM\{
    Metadatas,
    Entity\DataExtractorInterface,
    Entity\DataExtractor
};
use Innmind\Immutable\Map;

final class DataExtractorFactory
{
    public static function make(Metadatas $metadatas, array $extractors): DataExtractor
    {
        $map = new Map('string', DataExtractorInterface::class);

        foreach ($extractors as $metadata => $extractor) {
            $map = $map->put($metadata, $extractor);
        }

        return new DataExtractor($metadatas, $map);
    }
}
