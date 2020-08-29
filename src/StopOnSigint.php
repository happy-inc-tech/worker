<?php

declare(strict_types=1);

namespace HappyInc\Worker;

final class StopOnSigint
{
    /**
     * @var bool
     */
    private $stop = false;

    public function __construct()
    {
        pcntl_signal(SIGINT, function (): void {
            $this->stop = true;
        });
    }

    public function __invoke(WorkerDoneJob $event): void
    {
        pcntl_signal_dispatch();

        if ($this->stop) {
            $event->stop('Process received a SIGINT signal.');
        }
    }
}
