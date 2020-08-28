<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\EventDispatcher\StoppableEventInterface;

final class WorkerTicked implements StoppableEventInterface
{
    /**
     * @var int
     * @psalm-readonly
     */
    public $tick;

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

    public function __construct(int $tick)
    {
        $this->tick = $tick;
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
