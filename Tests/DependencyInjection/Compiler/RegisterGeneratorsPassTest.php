<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\Tests\DependencyInjection\Compiler;

use Innmind\Neo4jBundle\DependencyInjection\{
    Compiler\RegisterGeneratorsPass,
    InnmindNeo4jExtension
};
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Reference,
    Definition,
    Compiler\CompilerPassInterface
};

class RegisterGeneratorsPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $c = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $c);
        $c->setDefinition(
            'foo',
            (new Definition)
                ->addTag('innmind_neo4j.identity.generator', ['class' => 'stdClass'])
        );
        $this->assertSame(
            null,
            ($p = new RegisterGeneratorsPass)->process($c)
        );
        $this->assertInstanceOf(CompilerPassInterface::class, $p);

        $definition = $c->getDefinition('innmind_neo4j.generators');
        $calls = $definition->getMethodCalls();

        $this->assertSame(1, count($calls));
        $this->assertSame('register', $calls[0][0]);
        $this->assertSame('stdClass', $calls[0][1][0]);
        $this->assertInstanceOf(Reference::class, $calls[0][1][1]);
        $this->assertSame('foo', (string) $calls[0][1][1]);
    }

    /**
     * @expectedException Innmind\Neo4jBundle\Exception\RuntimeException
     * @expectedExceptionMessage The class attribute must be defined
     */
    public function testThrowWhenNoClassForGenerator()
    {
        $c = new ContainerBuilder;
        (new InnmindNeo4jExtension)->load([], $c);
        $c->setDefinition(
            'foo',
            (new Definition)
                ->addTag('innmind_neo4j.identity.generator')
        );
        (new RegisterGeneratorsPass)->process($c);
    }
}
