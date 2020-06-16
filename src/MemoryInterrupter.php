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

    public function __construct(int $maxBytes)
    {
        if ($maxBytes <= 0) {
            throw new \InvalidArgumentException(sprintf('Parameter $maxBytes must be a positive integer, got %d.', $maxBytes));
        }

        $this->maxBytes = $maxBytes;
    }

    public function __invoke(Context $context): void
    {
        $usedMemory = memory_get_usage(true);

        if ($usedMemory < $this->maxBytes) {
            return;
        }

        $context->stop();
        $context->log('Worker stopped after exceeding the memory limit of {limit} bytes ({memory} bytes used).', [
            'limit' => $this->maxBytes,
            'memory' => $usedMemory,
        ], LogLevel::WARNING);
    }
}
