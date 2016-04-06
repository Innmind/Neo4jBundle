<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Factory;

use Innmind\Immutable\Map;

class MapFactory
{
    public static function make(string $class, string $type, array $translators)
    {
        $map = new Map('string', $type);

        foreach ($translators as $meta => $translator) {
            $map = $map->put($meta, $translator);
        }

        return new $class($map);
    }
}
