<?php

declare(strict_types=1);

namespace HappyInc\Worker\ProcessSignal;

use HappyInc\Worker\Context;
use HappyInc\Worker\Stop;
use HappyInc\Worker\WorkerJobDoneExtension;
use HappyInc\Worker\WorkerStartedExtension;

final class ProcessSignalExtension implements WorkerStartedExtension, WorkerJobDoneExtension
{
    /**
     * @psalm-var non-empty-list<int>
     */
    private array $signals;

    /**
     * @psalm-param non-empty-list<int> $signals
     */
    public function __construct(array $signals)
    {
        $this->signals = $signals;
    }

    public function started(Context $context): void
    {
        /** @var ProcessSignalContextData $data */
        $data = $context->dataOf(ProcessSignalContextData::class);

        $handler = static function (int $signal) use ($data): void {
            $data->receivedSignal = $signal;
        };

        foreach ($this->signals as $signal) {
            pcntl_signal($signal, $handler);
        }
    }

    public function jobDone(Context $context, int $jobIndex): ?Stop
    {
        pcntl_signal_dispatch();

        /** @var ProcessSignalContextData $data */
        $data = $context->dataOf(ProcessSignalContextData::class);

        if (null === $data->receivedSignal) {
            return null;
        }

        return new Stop(sprintf('Process received signal %d.', $data->receivedSignal));
    }
}
