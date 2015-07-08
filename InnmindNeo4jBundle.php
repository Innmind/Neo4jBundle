<?php

namespace Innmind\Neo4jBundle;

use Innmind\Neo4jBundle\DependencyInjection\Compiler\ComputeBundlesPathPass;
use Innmind\Neo4jBundle\DependencyInjection\Compiler\RegisterManagersPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InnmindNeo4jBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container
            ->addCompilerPass(
                new ComputeBundlesPathPass
            )
            ->addCompilerPass(
                new RegisterManagersPass
            );
    }
}
