<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Factory;

use Innmind\Neo4j\ONM\{
    PersisterInterface,
    Persister\DelegationPersister
};
use Innmind\Immutable\Stream;

class DelegationPersisterFactory
{
    /**
     * Build the delegation persister
     *
     * @param array<PersisterInterface> $persisters
     *
     * @return DelegationPersister
     */
    public static function make(array $persisters): DelegationPersister
    {
        $stream = new Stream(PersisterInterface::class);

        foreach ($persisters as $persister) {
            $stream = $stream->add($persister);
        }

        return new DelegationPersister($stream);
    }
}
