<?php

declare(strict_types=1);

namespace HappyInc\Worker\Sleep;

/**
 * @psalm-immutable
 */
final class SleepInterval
{
    /**
     * @param positive-int $microseconds
     */
    private function __construct(public int $microseconds)
    {
    }

    /**
     * @psalm-pure
     */
    public static function fromMicroseconds(int $microseconds): self
    {
        if ($microseconds <= 0) {
            throw new \InvalidArgumentException('Sleep interval must be zero or positive.');
        }

        return new self($microseconds);
    }

    /**
     * @psalm-pure
     */
    public static function fromMilliseconds(int $milliseconds): self
    {
        if ($milliseconds <= 0) {
            throw new \InvalidArgumentException('Sleep interval must be zero or positive.');
        }

        return new self($milliseconds * 1000);
    }

    /**
     * @psalm-pure
     */
    public static function fromSeconds(int $seconds): self
    {
        if ($seconds <= 0) {
            throw new \InvalidArgumentException('Sleep interval must be zero or positive.');
        }

        return new self($seconds * 1_000_000);
    }
}
