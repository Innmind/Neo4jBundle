<?php

namespace Innmind\Neo4jBundle;

use Innmind\Neo4jBundle\DependencyInjection\Compiler\RegisterEntityTranslatorsPass;
use Symfony\Component\{
    HttpKernel\Bundle\Bundle,
    DependencyInjection\ContainerBuilder
};

class InnmindNeo4jBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container
            ->addCompilerPass(new RegisterEntityTranslatorsPass);
    }
}
