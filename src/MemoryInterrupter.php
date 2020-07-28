<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\Log\LogLevel;

final class MemoryInterrupter
{
    /**
     * @var int
     */
    private $maxBytes;

    /**
     * @psalm-var callable(): int
     */
    private $memoryGetUsage;

    /**
     * @psalm-param ?callable(): int $memoryGetUsage
     */
    public function __construct(int $maxBytes, ?callable $memoryGetUsage = null)
    {
        if ($maxBytes <= 0) {
            throw new \InvalidArgumentException(sprintf('Parameter $maxBytes must be a positive integer, got %d.', $maxBytes));
        }

        $this->maxBytes = $maxBytes;
        $this->memoryGetUsage = $memoryGetUsage ?? static function (): int { return memory_get_usage(true); };
    }

    public function __invoke(Context $context): void
    {
        $usedMemory = ($this->memoryGetUsage)();

        if ($usedMemory < $this->maxBytes) {
            return;
        }

        $context->stop();

        $context->log(
            'Worker stopped after exceeding the memory limit of {limit} bytes ({memory} bytes used).',
            ['limit' => $this->maxBytes, 'memory' => $usedMemory],
            LogLevel::WARNING
        );
    }
}
