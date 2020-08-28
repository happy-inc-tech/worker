<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @psalm-import-type Operation from Worker
 */
final class WorkerBuilder
{
    /**
     * @psalm-var Operation
     */
    private $operation;

    /**
     * @psalm-var array{
     *     'HappyInc\Worker\WorkerStarted'?: list<callable(WorkerStarted): void>,
     *     'HappyInc\Worker\WorkerTicked'?: list<callable(WorkerTicked): void>,
     *     'HappyInc\Worker\WorkerStopped'?: list<callable(WorkerStopped): void>,
     * }
     */
    private $listeners = [];

    /**
     * @var EventDispatcherInterface|null
     */
    private $eventDispatcher;

    /**
     * @psalm-var positive-int
     */
    private $tickIntervalSeconds = 1;

    /**
     * @var int|null
     */
    private $memorySoftLimitBytes;

    /**
     * @var bool
     */
    private $stopOnSigterm = true;

    /**
     * @var StopSignaller|null
     */
    private $stopSignaller;

    /**
     * @var string|null
     */
    private $stopSignalChannel;

    /**
     * @psalm-param Operation $operation
     */
    private function __construct(callable $operation)
    {
        $this->operation = $operation;
    }

    /**
     * @psalm-param Operation $operation
     */
    public static function create(callable $operation): self
    {
        return new self($operation);
    }

    /**
     * @psalm-param callable(WorkerStarted): void $listener
     */
    public function addWorkerStartedListener(callable $listener): self
    {
        $this->listeners[WorkerStarted::class][] = $listener;

        return $this;
    }

    /**
     * @psalm-param callable(WorkerTicked): void $listener
     */
    public function addWorkerTickedListener(callable $listener): self
    {
        $this->listeners[WorkerTicked::class][] = $listener;

        return $this;
    }

    /**
     * @psalm-param callable(WorkerStopped): void $listener
     */
    public function addWorkerStoppedListener(callable $listener): self
    {
        $this->listeners[WorkerStopped::class][] = $listener;

        return $this;
    }

    public function setEventDispatcher(?EventDispatcherInterface $eventDispatcher): self
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @psalm-param positive-int $tickIntervalSeconds
     */
    public function setTickInterval(int $tickIntervalSeconds): self
    {
        $this->tickIntervalSeconds = $tickIntervalSeconds;

        return $this;
    }

    public function setMemorySoftLimit(?int $memorySoftLimitBytes): self
    {
        $this->memorySoftLimitBytes = $memorySoftLimitBytes;

        return $this;
    }

    public function setStopSignaller(?StopSignaller $stopSignaller): self
    {
        $this->stopSignaller = $stopSignaller;

        return $this;
    }

    public function setStopSignalChannel(?string $stopSignalChannel): self
    {
        $this->stopSignalChannel = $stopSignalChannel;

        return $this;
    }

    public function build(): Worker
    {
        $listeners = $this->listeners;

        if (null !== $this->memorySoftLimitBytes) {
            $listeners[WorkerTicked::class][] = new StopOnMemorySoftLimitExceeded($this->memorySoftLimitBytes);
        }

        if ($this->stopOnSigterm) {
            $listeners[WorkerTicked::class][] = new StopOnSigterm();
        }

        if (null !== $this->stopSignalChannel) {
            if (null === $this->stopSignaller) {
                $this->stopSignaller = new FileStopSignaller();
            }

            $listeners[WorkerTicked::class][] = $this->stopSignaller->createListener($this->stopSignalChannel);
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        return new Worker($this->operation, new EventDispatcher($listeners, $this->eventDispatcher), $this->tickIntervalSeconds);
    }
}
