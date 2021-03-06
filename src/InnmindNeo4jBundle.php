<?php

namespace Innmind\Neo4jBundle;

use Innmind\Neo4jBundle\DependencyInjection\Compiler\{
    RegisterTagMapPass,
    RegisterRepositoriesPass,
    RegisterEntityFactoriesPass,
    RegisterMetadataFactoriesPass,
    InjectEntityDefinitionsPass,
    RegisterIdentityGeneratorsPass
};
use Symfony\Component\{
    HttpKernel\Bundle\Bundle,
    DependencyInjection\ContainerBuilder
};

final class InnmindNeo4jBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container
            ->addCompilerPass(new RegisterTagMapPass(
                'innmind_neo4j.translator.result',
                'innmind_neo4j.translation.result'
            ))
            ->addCompilerPass(new RegisterIdentityGeneratorsPass)
            ->addCompilerPass(new RegisterRepositoriesPass)
            ->addCompilerPass(new RegisterEntityFactoriesPass)
            ->addCompilerPass(new RegisterTagMapPass(
                'innmind_neo4j.translator.identity_match',
                'innmind_neo4j.translation.identity_match'
            ))
            ->addCompilerPass(new RegisterTagMapPass(
                'innmind_neo4j.translator.match',
                'innmind_neo4j.translation.match'
            ))
            ->addCompilerPass(new RegisterTagMapPass(
                'innmind_neo4j.translator.specification',
                'innmind_neo4j.translation.specification'
            ))
            ->addCompilerPass(new RegisterMetadataFactoriesPass)
            ->addCompilerPass(new InjectEntityDefinitionsPass);
    }
}
