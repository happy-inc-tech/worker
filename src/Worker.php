<?php

declare(strict_types=1);

namespace HappyInc\Worker;

final class Worker
{
    /**
     * @var WorkerStartedExtension[]
     */
    private array $startedExtensions = [];

    /**
     * @var WorkerJobDoneExtension[]
     */
    private array $jobDoneExtensions = [];

    /**
     * @var WorkerStoppedExtension[]
     */
    private array $stoppedExtensions = [];

    /**
     * @psalm-param iterable<WorkerStartedExtension|WorkerJobDoneExtension|WorkerStoppedExtension> $extensions
     */
    public function __construct(iterable $extensions = [])
    {
        foreach ($extensions as $extension) {
            if ($extension instanceof WorkerStartedExtension) {
                $this->startedExtensions[] = $extension;
            }

            if ($extension instanceof WorkerJobDoneExtension) {
                $this->jobDoneExtensions[] = $extension;
            }

            if ($extension instanceof WorkerStoppedExtension) {
                $this->stoppedExtensions[] = $extension;
            }
        }
    }

    /**
     * @psalm-param callable(Context, int): void $job
     */
    public function workOn(callable $job): Result
    {
        $context = new Context();

        foreach ($this->startedExtensions as $extension) {
            $extension->started($context);
        }

        $stop = null;

        for ($jobIndex = 0;; ++$jobIndex) {
            $job($context, $jobIndex);

            foreach ($this->jobDoneExtensions as $extension) {
                $stop = $extension->jobDone($context, $jobIndex);

                if (null !== $stop) {
                    break 2;
                }
            }
        }

        $result = new Result($jobIndex + 1, $stop->reason ?? null);

        foreach ($this->stoppedExtensions as $extension) {
            $extension->stopped($context, $result);
        }

        return $result;
    }
}
