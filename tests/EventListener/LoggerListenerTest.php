<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4jBundle\EventListener;

use Innmind\Neo4jBundle\EventListener\LoggerListener;
use Innmind\Neo4j\DBAL\{
    Events,
    Event\PreQueryEvent,
    Cypher
};
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;

class LoggerListenerTest extends TestCase
{
    public function testLog()
    {
        $called = false;
        $l = new LoggerListener(
           $m = $this->createMock(LoggerInterface::class)
        );
        $m
            ->method('info')
            ->will($this->returnCallback(function($message, $context) use (&$called) {
                $called = true;
                $this->assertSame('Cypher query about to be executed', $message);
                $this->assertSame('foo', $context['cypher']);
                $this->assertSame(
                    ['bar' => 'baz'],
                    $context['parameters']
                );
            }));
        $this->assertSame(
            null,
            $l->log(
                new PreQueryEvent(
                    (new Cypher('foo'))
                        ->withParameter('bar', 'baz')
                )
            )
        );
        $this->assertTrue($called);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertSame(
            [Events::PRE_QUERY => 'log'],
            LoggerListener::getSubscribedEvents()
        );
    }
}
