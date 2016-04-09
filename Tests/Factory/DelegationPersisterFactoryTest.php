<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Tests\Factory;

use Innmind\Neo4jBundle\Factory\DelegationPersisterFactory;
use Innmind\Neo4j\ONM\{
    PersisterInterface,
    Persister\DelegationPersister
};

class DelegationPersisterFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        $p = DelegationPersisterFactory::make([
            $this->getMock(PersisterInterface::class)
        ]);

        $this->assertInstanceOf(DelegationPersister::class, $p);
    }
}
