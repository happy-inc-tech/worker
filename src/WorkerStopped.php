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
    public $tickedTimes;

    /**
     * @var string|null
     */
    public $stopReason;

    /**
     * @psalm-param positive-int $tickedTimes
     */
    public function __construct(int $tickedTimes, ?string $stopReason)
    {
        $this->tickedTimes = $tickedTimes;
        $this->stopReason = $stopReason;
    }
}
