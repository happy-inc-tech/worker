<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @psalm-type Job = callable(WorkerJobContext): void
 */
final class Worker
{
    /**
     * @psalm-var Job
     */
    private $job;

    /**
     * @var RestInterval
     */
    private $restInterval;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @psalm-param Job $job
     */
    public function __construct(callable $job, ?RestInterval $restInterval = null, ?EventDispatcherInterface $eventDispatcher = null)
    {
        $this->job = $job;
        $this->restInterval = $restInterval ?? RestInterval::fromSeconds(1);
        $this->eventDispatcher = $eventDispatcher ?? new NullEventDispatcher();
    }

    public function do(): WorkerStopped
    {
        $this->eventDispatcher->dispatch(new WorkerStarted());

        $jobIndex = 0;
        $stopReason = null;

        while (true) {
            $jobContext = new WorkerJobContext($jobIndex);
            ($this->job)($jobContext);

            if ($jobContext->stopped) {
                $stopReason = $jobContext->stopReason;

                break;
            }

            $doneJob = new WorkerDoneJob($jobIndex);
            $this->eventDispatcher->dispatch($doneJob);

            if ($doneJob->stopped) {
                $stopReason = $doneJob->stopReason;

                break;
            }

            usleep($this->restInterval->microseconds);
            ++$jobIndex;
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        $stopped = new WorkerStopped($jobIndex + 1, $stopReason);
        $this->eventDispatcher->dispatch($stopped);

        return $stopped;
    }
}
