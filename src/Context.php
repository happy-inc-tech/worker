<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class Context
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $tick = 0;

    /**
     * @var bool
     */
    private $stopped = false;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function tick(): int
    {
        return $this->tick;
    }

    public function log(string $message, array $context = [], string $level = LogLevel::INFO): void
    {
        $this->logger->log($level, $message, $context);
    }

    public function stop(): void
    {
        $this->stopped = true;
    }

    public function isStopped(): bool
    {
        return $this->stopped;
    }

    public function next(): self
    {
        $next = new self($this->logger);
        ++$next->tick;

        return $next;
    }
}
