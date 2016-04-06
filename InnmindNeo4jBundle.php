<?php

namespace Innmind\Neo4jBundle;

use Innmind\Neo4jBundle\DependencyInjection\Compiler\{
    RegisterTagMapPass,
    RegisterGeneratorsPass,
    RegisterEntityFactoriesPass
};
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
            ->addCompilerPass(new RegisterTagMapPass(
                'innmind_neo4j.translator.result',
                'innmind_neo4j.translation.result'
            ))
            ->addCompilerPass(new RegisterGeneratorsPass)
            ->addCompilerPass(new RegisterEntityFactoriesPass);
    }
}
