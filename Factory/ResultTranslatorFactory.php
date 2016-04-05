<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Factory;

use Innmind\Neo4j\ONM\Translation\{
    ResultTranslator,
    EntityTranslatorInterface
};
use Innmind\Immutable\Map;

class ResultTranslatorFactory
{
    public static function make(array $translators): ResultTranslator
    {
        $map = new Map('string', EntityTranslatorInterface::class);

        foreach ($translators as $meta => $translator) {
            $map = $map->put($meta, $translator);
        }

        return new ResultTranslator($map);
    }
}
