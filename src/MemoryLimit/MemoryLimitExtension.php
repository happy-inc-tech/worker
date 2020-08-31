<?php

declare(strict_types=1);

namespace HappyInc\Worker\MemoryLimit;

use HappyInc\Worker\Context;
use HappyInc\Worker\Stop;
use HappyInc\Worker\WorkerJobDoneExtension;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

final class MemoryLimitExtension implements WorkerJobDoneExtension
{
    private MemoryLimit $limit;

    private LoggerInterface $logger;

    /**
     * @psalm-var LogLevel::*
     */
    private string $logLevel;

    /**
     * @psalm-param LogLevel::* $logLevel
     */
    public function __construct(MemoryLimit $limit, ?LoggerInterface $logger = null, string $logLevel = LogLevel::WARNING)
    {
        $this->limit = $limit;
        $this->logger = $logger ?? new NullLogger();
        $this->logLevel = $logLevel;
    }

    public function jobDone(Context $context, int $jobIndex): ?Stop
    {
        $allocatedMemory = memory_get_usage(true);

        if ($allocatedMemory <= $this->limit->bytes) {
            return null;
        }

        $this->logger->log(
            $this->logLevel,
            'Allocated memory of {allocated} bytes exceeded the worker limit of {limit} bytes.',
            [
                'allocated' => $allocatedMemory,
                'limit' => $this->limit->bytes,
            ]
        );

        return new Stop(sprintf(
            'Allocated memory of %d bytes exceeded the worker limit of %d bytes.',
            $allocatedMemory,
            $this->limit->bytes
        ));
    }
}
