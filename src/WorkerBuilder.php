<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @psalm-import-type Job from Worker
 */
final class WorkerBuilder
{
    /**
     * @psalm-var ?Job
     */
    private $job;

    /**
     * @var RestInterval|null
     */
    private $restInterval;

    /**
     * @psalm-var array{
     *     'HappyInc\Worker\WorkerStarted'?: list<callable(WorkerStarted): void>,
     *     'HappyInc\Worker\WorkerDoneJob'?: list<callable(WorkerDoneJob): void>,
     *     'HappyInc\Worker\WorkerStopped'?: list<callable(WorkerStopped): void>,
     * }
     */
    private $listeners = [];

    /**
     * @var EventDispatcherInterface|null
     */
    private $eventDispatcher;

    /**
     * @var int|null
     */
    private $memorySoftLimitBytes;

    /**
     * @var LoggerInterface|null
     */
    private $memorySoftLimitLogger;

    /**
     * @var string
     * @psalm-var LogLevel::*
     */
    private $memorySoftLimitLogLevel = LogLevel::WARNING;

    /**
     * @var bool
     */
    private $stopOnSigint = true;

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

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    /**
     * @psalm-param Job $job
     */
    public function setJob(callable $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function setRestInterval(?RestInterval $restInterval): self
    {
        $this->restInterval = $restInterval;

        return $this;
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
     * @psalm-param callable(WorkerDoneJob): void $listener
     */
    public function addWorkerDoneJobListener(callable $listener): self
    {
        $this->listeners[WorkerDoneJob::class][] = $listener;

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

    public function setMemorySoftLimitBytes(?int $memorySoftLimitBytes): self
    {
        $this->memorySoftLimitBytes = $memorySoftLimitBytes;

        return $this;
    }

    public function setMemorySoftLimitLogger(?LoggerInterface $memorySoftLimitLogger): self
    {
        $this->memorySoftLimitLogger = $memorySoftLimitLogger;

        return $this;
    }

    /**
     * @psalm-param LogLevel::* $memorySoftLimitLogLevel
     */
    public function setMemorySoftLimitLogLevel(string $memorySoftLimitLogLevel): self
    {
        $this->memorySoftLimitLogLevel = $memorySoftLimitLogLevel;

        return $this;
    }

    public function setStopOnSigint(bool $stopOnSigint): self
    {
        $this->stopOnSigint = $stopOnSigint;

        return $this;
    }

    public function setStopOnSigterm(bool $stopOnSigterm): self
    {
        $this->stopOnSigterm = $stopOnSigterm;

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
        if (null === $this->job) {
            throw new \LogicException('Job is required to build a worker.');
        }

        $listeners = $this->listeners;

        if (null !== $this->memorySoftLimitBytes) {
            $listeners[WorkerDoneJob::class][] = new StopOnMemorySoftLimitExceeded(
                $this->memorySoftLimitBytes,
                $this->memorySoftLimitLogger,
                $this->memorySoftLimitLogLevel
            );
        }

        if ($this->stopOnSigint) {
            $listeners[WorkerDoneJob::class][] = new StopOnSigint();
        }

        if ($this->stopOnSigterm) {
            $listeners[WorkerDoneJob::class][] = new StopOnSigterm();
        }

        if (null !== $this->stopSignalChannel) {
            $listeners[WorkerDoneJob::class][] = ($this->stopSignaller ?? new FileStopSignaller())->createListener($this->stopSignalChannel);
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        return new Worker($this->job, $this->restInterval, new EventDispatcher($listeners, $this->eventDispatcher));
    }
}
