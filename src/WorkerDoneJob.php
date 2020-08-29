<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\EventDispatcher\StoppableEventInterface;

final class WorkerDoneJob implements StoppableEventInterface
{
    /**
     * @var int
     * @psalm-readonly
     */
    public $jobIndex;

    /**
     * @var bool
     * @psalm-readonly-allow-private-mutation
     */
    public $stopped = false;

    /**
     * @var string|null
     * @psalm-readonly-allow-private-mutation
     */
    public $stopReason;

    public function __construct(int $jobIndex)
    {
        $this->jobIndex = $jobIndex;
    }

    public function stop(?string $reason = null): void
    {
        $this->stopped = true;
        $this->stopReason = $reason;
    }

    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }
}
