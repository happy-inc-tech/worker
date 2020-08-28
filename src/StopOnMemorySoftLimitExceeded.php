<?php

declare(strict_types=1);

namespace HappyInc\Worker;

final class StopOnMemorySoftLimitExceeded
{
    /**
     * @var int
     */
    private $softLimitBytes;

    /**
     * @psalm-var callable(): int
     */
    private $memoryGetUsage;

    /**
     * @psalm-param ?callable(): int $memoryGetUsage
     */
    public function __construct(int $softLimitBytes, ?callable $memoryGetUsage = null)
    {
        if ($softLimitBytes <= 0) {
            throw new \InvalidArgumentException(sprintf('Parameter $maxBytes must be a positive integer, got %d.', $softLimitBytes));
        }

        $this->softLimitBytes = $softLimitBytes;
        $this->memoryGetUsage = $memoryGetUsage ?? static function (): int { return memory_get_usage(true); };
    }

    public function __invoke(WorkerTicked $event): void
    {
        $allocatedMemory = ($this->memoryGetUsage)();

        if ($allocatedMemory <= $this->softLimitBytes) {
            return;
        }

        $event->stop(sprintf('Allocated memory of %d bytes exceeded the soft limit of %d bytes.', $allocatedMemory, $this->softLimitBytes));
    }
}
