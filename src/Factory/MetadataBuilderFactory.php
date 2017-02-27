<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Factory;

use Innmind\Neo4j\ONM\{
    Types,
    MetadataFactoryInterface,
    MetadataBuilder
};
use Innmind\Immutable\Map;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class MetadataBuilderFactory
{
    /**
     * Create a new metadata builder
     *
     * @param Types $types
     * @param array $factories
     * @param ConfigurationInterface $config
     *
     * @return MetadataBuilder
     */
    public static function make(
        Types $types,
        array $factories,
        ConfigurationInterface $config
    ): MetadataBuilder {
        $map = new Map('string', MetadataFactoryInterface::class);

        foreach ($factories as $meta => $factory) {
            $map = $map->put($meta, $factory);
        }

        return new MetadataBuilder($types, $map, $config);
    }
}
