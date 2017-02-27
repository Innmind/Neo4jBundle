<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\DependencyInjection\Compiler\RegisterIdentityGeneratorsPass;
use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Definition,
    Reference
};
use PHPUnit\Framework\TestCase;

class RegisterIdentityGeneratorsPassTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CompilerPassInterface::class,
            new RegisterIdentityGeneratorsPass
        );
    }

    public function testProcess()
    {
        $container = new ContainerBuilder;
        $container->setDefinition(
            'innmind_neo4j.generators',
            $definition = new Definition(
                'foo',
                [
                    'class',
                    'type',
                    []
                ]
            )
        );
        $container->setDefinition(
            'foo',
            (new Definition)->addTag(
                'innmind_neo4j.identity.generator',
                ['generates' => 'foobar']
            )
        );
        $this->assertNull(
            (new RegisterIdentityGeneratorsPass)->process($container)
        );

        $this->assertCount(1, $definition->getArgument(2));
        $this->assertSame('foobar', key($definition->getArgument(2)));
        $this->assertInstanceOf(Reference::class, $definition->getArgument(2)['foobar']);
        $this->assertSame('foo', (string) $definition->getArgument(2)['foobar']);
    }

    /**
     * @expectedException Innmind\Neo4jBundle\Exception\LogicException
     * @expectedExceptionMessage The "generates" attribute must be set for "foo"
     */
    public function testThrowWhenNoGeneratesAttribute()
    {
        $container = new ContainerBuilder;
        $container->setDefinition(
            'innmind_neo4j.generators',
            $definition = new Definition(
                'foo',
                [
                    'class',
                    'type',
                    []
                ]
            )
        );
        $container->setDefinition(
            'foo',
            (new Definition)->addTag('innmind_neo4j.identity.generator')
        );
        (new RegisterIdentityGeneratorsPass)->process($container);
    }
}
