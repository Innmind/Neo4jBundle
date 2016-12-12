<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Factory;

use Innmind\Immutable\Map;

class MapFactory
{
    public static function make(string $class, string $type, array $elements)
    {
        $map = new Map('string', $type);

        foreach ($elements as $key => $element) {
            $map = $map->put($key, $element);
        }

        return new $class($map);
    }
}
