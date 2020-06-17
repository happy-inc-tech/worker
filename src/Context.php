<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class Context
{
    /**
     * @var int
     */
    private $tick;

    /**
     * @var bool
     */
    private $stopped = false;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(int $tick, LoggerInterface $logger)
    {
        $this->tick = $tick;
        $this->logger = $logger;
    }

    public function getTick(): int
    {
        return $this->tick;
    }

    public function isStopped(): bool
    {
        return $this->stopped;
    }

    public function stop(): void
    {
        $this->stopped = true;
    }

    public function log(string $message, array $context = [], string $level = LogLevel::INFO): void
    {
        $this->logger->log($level, $message, $context);
    }
}
