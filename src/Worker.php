<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\EventDispatcher\EventDispatcherInterface;

final class Worker
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(?EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher ?? new NullEventDispatcher();
    }

    /**
     * @psalm-param callable(WorkerJobContext): void $job
     */
    public function do(callable $job, RestInterval $restInterval): WorkerStopped
    {
        $this->eventDispatcher->dispatch(new WorkerStarted());

        $jobIndex = 0;
        $stopReason = null;

        while (true) {
            $jobContext = new WorkerJobContext($jobIndex);
            $job($jobContext);

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

            usleep($restInterval->microseconds);
            ++$jobIndex;
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        $stopped = new WorkerStopped($jobIndex + 1, $stopReason);
        $this->eventDispatcher->dispatch($stopped);

        return $stopped;
    }
}
