<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 * @psalm-internal \HappyInc\Worker
 */
final class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @psalm-var array<class-string, list<callable(object): void>>
     */
    private $listeners;

    /**
     * @var EventDispatcherInterface|null
     */
    private $decoratedDispatcher;

    /**
     * @psalm-param array<class-string, list<callable(object): void>> $listeners
     */
    public function __construct(array $listeners = [], ?EventDispatcherInterface $decoratedDispatcher = null)
    {
        $this->listeners = $listeners;
        $this->decoratedDispatcher = $decoratedDispatcher;
    }

    /**
     * @template T of object
     * @psalm-param T $event
     */
    public function dispatch(object $event): object
    {
        /** @psalm-var list<callable(T): void> */
        $listeners = $this->listeners[\get_class($event)] ?? [];

        foreach ($listeners as $listener) {
            $listener($event);
        }

        if (null === $this->decoratedDispatcher) {
            return $event;
        }

        return $this->decoratedDispatcher->dispatch($event);
    }
}
