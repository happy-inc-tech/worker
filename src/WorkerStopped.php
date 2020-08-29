<?php

declare(strict_types=1);

namespace HappyInc\Worker;

/**
 * @psalm-immutable
 */
final class WorkerStopped
{
    /**
     * @psalm-var positive-int
     */
    public $jobs;

    /**
     * @var string|null
     */
    public $stopReason;

    /**
     * @psalm-param positive-int $jobs
     */
    public function __construct(int $jobs, ?string $stopReason)
    {
        $this->jobs = $jobs;
        $this->stopReason = $stopReason;
    }
}
