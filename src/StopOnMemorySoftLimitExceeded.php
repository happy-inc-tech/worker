<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

final class StopOnMemorySoftLimitExceeded
{
    /**
     * @var int
     */
    private $limitBytes;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @psalm-var LogLevel::*
     */
    private $logLevel;

    /**
     * @psalm-param ?callable(): int $memoryGetUsage
     * @psalm-param LogLevel::* $logLevel
     */
    public function __construct(int $limitBytes, ?LoggerInterface $logger = null, string $logLevel = LogLevel::WARNING)
    {
        if ($limitBytes <= 0) {
            throw new \InvalidArgumentException(sprintf('Parameter $maxBytes must be a positive integer, got %d.', $limitBytes));
        }

        $this->limitBytes = $limitBytes;
        $this->logger = $logger ?? new NullLogger();
        $this->logLevel = $logLevel;
    }

    public function __invoke(WorkerDoneJob $event): void
    {
        $allocatedMemory = memory_get_usage(true);

        if ($allocatedMemory <= $this->limitBytes) {
            return;
        }

        $event->stop(sprintf('Allocated memory of %d bytes exceeded the soft limit of %d bytes.', $allocatedMemory, $this->limitBytes));
        $this->logger->log(
            $this->logLevel,
            'Allocated memory of {allocated_bytes} bytes exceeded the soft limit of {limit_bytes} bytes.',
            [
                'allocated_bytes' => $allocatedMemory,
                'limit_bytes' => $this->limitBytes,
            ]
        );
    }
}
