<?php
declare(strict_types = 1);

namespace Innmind\Neo4jBundle\EventListener;

use Innmind\Neo4j\DBAL\{
    Events,
    Event\PreQueryEvent,
    Query\Parameter
};
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::PRE_QUERY => 'log',
        ];
    }

    /**
     * Log the query about to be run
     *
     * @param PreQueryEvent $event
     *
     * @return void
     */
    public function log(PreQueryEvent $event)
    {
        $parameters = [];
        $event
            ->query()
            ->parameters()
            ->each(function(int $idx, Parameter $param) use (&$parameters) {
                $parameters[$param->key()] = $param->value();
            });

        $this->logger->info(
            'Cypher query about to be executed',
            [
                'cypher' => (string) $event->query(),
                'parameters' => $parameters,
            ]
        );
    }
}
