<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\DependencyInjection\{
    Compiler\RegisterRepositoriesPass,
    InnmindNeo4jExtension
};
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Reference,
    Definition,
    Compiler\CompilerPassInterface
};
use PHPUnit\Framework\TestCase;

class RegisterRepositoriesPassTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CompilerPassInterface::class,
            new RegisterRepositoriesPass
        );
    }

    public function testProcessRepositoryFactoryConfigurator()
    {
        $container = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $container);
        $container->setDefinition(
            'foo',
            (new Definition)
                ->addTag('innmind_neo4j.repository', ['class' => 'stdClass'])
        );
        $this->assertNull(
            (new RegisterRepositoriesPass)->process($container)
        );

        $definition = $container->getDefinition('innmind_neo4j.repository_factory.configurator');
        $calls = $definition->getMethodCalls();

        $this->assertCount(1, $calls);
        $this->assertSame('register', $calls[0][0]);
        $this->assertSame('stdClass', $calls[0][1][0]);
        $this->assertInstanceOf(Reference::class, $calls[0][1][1]);
        $this->assertSame('foo', (string) $calls[0][1][1]);
    }

    /**
     * @expectedException Innmind\Neo4jBundle\Exception\RuntimeException
     * @expectedExceptionMessage The class attribute must be defined
     */
    public function testThrowWhenNoClassForRepository()
    {
        $container = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $container);
        $container->setDefinition(
            'foo',
            (new Definition)
                ->addTag('innmind_neo4j.repository')
        );
        (new RegisterRepositoriesPass)->process($container);
    }
}
