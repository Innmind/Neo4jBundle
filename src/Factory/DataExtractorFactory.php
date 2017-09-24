<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Factory;

use Innmind\Neo4j\ONM\{
    Metadatas,
    Entity\DataExtractor
};
use Innmind\Immutable\Map;

final class DataExtractorFactory
{
    public static function make(Metadatas $metadatas, array $extractors): DataExtractor\DataExtractor
    {
        $map = new Map('string', DataExtractor::class);

        foreach ($extractors as $metadata => $extractor) {
            $map = $map->put($metadata, $extractor);
        }

        return new DataExtractor\DataExtractor($metadatas, $map);
    }
}
