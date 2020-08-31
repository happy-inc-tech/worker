<?php

declare(strict_types=1);

namespace HappyInc\Worker\Sleep;

/**
 * @psalm-immutable
 */
final class SleepInterval
{
    public int $microseconds;

    private function __construct(int $microseconds)
    {
        $this->microseconds = $microseconds;
    }

    /**
     * @psalm-pure
     */
    public static function fromMicroseconds(int $microseconds): self
    {
        if ($microseconds < 0) {
            throw new \InvalidArgumentException(sprintf('Sleep interval must be zero or positive.'));
        }

        return new self($microseconds);
    }

    /**
     * @psalm-pure
     */
    public static function fromMilliseconds(int $milliseconds): self
    {
        if ($milliseconds < 0) {
            throw new \InvalidArgumentException(sprintf('Sleep interval must be zero or positive.'));
        }

        return new self($milliseconds * 1000);
    }

    /**
     * @psalm-pure
     */
    public static function fromSeconds(int $seconds): self
    {
        if ($seconds < 0) {
            throw new \InvalidArgumentException(sprintf('Sleep interval must be zero or positive.'));
        }

        return new self($seconds * 1_000_000);
    }
}
