<?php

declare(strict_types=1);

namespace HappyInc\Worker\Sleep;

use HappyInc\Worker\Context;
use HappyInc\Worker\Stop;
use HappyInc\Worker\WorkerJobDoneExtension;

final class SleepExtension implements WorkerJobDoneExtension
{
    private SleepInterval $interval;

    public function __construct(SleepInterval $interval)
    {
        $this->interval = $interval;
    }

    public function jobDone(Context $context, int $jobIndex): ?Stop
    {
        usleep($this->interval->microseconds);

        return null;
    }
}
