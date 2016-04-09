<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Factory;

use Innmind\Neo4j\ONM\{
    PersisterInterface,
    Persister\DelegationPersister
};
use Innmind\Immutable\Set;

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
        $set = new Set(PersisterInterface::class);

        foreach ($persisters as $persister) {
            $set = $set->add($persister);
        }

        return new DelegationPersister($set);
    }
}
