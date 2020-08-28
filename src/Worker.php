<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @psalm-type Operation = callable(WorkerTicked): void
 */
final class Worker
{
    /**
     * @psalm-var Operation
     */
    private $operation;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @psalm-var positive-int
     */
    private $sleepSeconds;

    /**
     * @psalm-param Operation $operation
     * @psalm-param positive-int $sleepSeconds
     */
    public function __construct(callable $operation, ?EventDispatcherInterface $eventDispatcher = null, int $sleepSeconds = 1)
    {
        $this->operation = $operation;
        $this->eventDispatcher = $eventDispatcher ?? new NullEventDispatcher();
        $this->sleepSeconds = $sleepSeconds;
    }

    public function run(): WorkerStopped
    {
        $this->eventDispatcher->dispatch(new WorkerStarted());

        $tick = 0;

        while (true) {
            $tickedEvent = new WorkerTicked($tick);

            ($this->operation)($tickedEvent);

            if ($tickedEvent->stopped) {
                break;
            }

            $this->eventDispatcher->dispatch($tickedEvent);

            /** @psalm-suppress DocblockTypeContradiction */
            if ($tickedEvent->stopped) {
                break;
            }

            sleep($this->sleepSeconds);
            ++$tick;
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        $stoppedEvent = new WorkerStopped($tick + 1, $tickedEvent->stopReason ?? null);
        $this->eventDispatcher->dispatch($stoppedEvent);

        return $stoppedEvent;
    }
}
