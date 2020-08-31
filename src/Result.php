<?php

declare(strict_types=1);

namespace HappyInc\Worker;

/**
 * @psalm-immutable
 */
final class Result
{
    public int $jobsDone;

    public ?string $stoppedReason;

    public function __construct(int $jobsDone, ?string $stoppedReason)
    {
        $this->jobsDone = $jobsDone;
        $this->stoppedReason = $stoppedReason;
    }
}
