<?php

declare(strict_types=1);

namespace HappyInc\Worker;

interface WorkerJobDoneExtension
{
    public function jobDone(Context $context, int $jobIndex): ?Stop;
}
