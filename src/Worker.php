<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Worker
{
    /**
     * @psalm-var iterable<callable(Context): void>
     */
    private $operations;

    /**
     * @var SigtermInterrupter
     */
    private $sigtermInterrupter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $sleepSeconds;

    /**
     * @psalm-param iterable<callable(Context): void> $operations
     */
    public function __construct(iterable $operations, ?LoggerInterface $logger = null, int $sleepSeconds = 1)
    {
        if ($sleepSeconds <= 0) {
            throw new \InvalidArgumentException(sprintf('Parameter $sleepSeconds must be a positive integer, got %d.', $sleepSeconds));
        }

        $this->operations = $operations;
        $this->sigtermInterrupter = new SigtermInterrupter();
        $this->logger = $logger ?? new NullLogger();
        $this->sleepSeconds = $sleepSeconds;
    }

    public function __invoke(): void
    {
        $context = new Context($this->logger);

        while (true) {
            foreach ($this->operations() as $operation) {
                $operation($context);

                if ($context->isStopped()) {
                    return;
                }
            }

            sleep($this->sleepSeconds);
            $context = $context->next();
        }
    }

    /**
     * @psalm-return \Generator<callable(Context): void>
     */
    private function operations(): \Generator
    {
        yield from $this->operations;
        yield $this->sigtermInterrupter;
    }
}
