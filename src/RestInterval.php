<?php

declare(strict_types=1);

namespace HappyInc\Worker;

/**
 * @psalm-immutable
 */
final class RestInterval
{
    /**
     * @var int
     */
    public $microseconds;

    private function __construct(int $microseconds)
    {
        $this->microseconds = $microseconds;
    }

    public static function fromMicroseconds(int $microseconds): self
    {
        if ($microseconds < 0) {
            throw new \InvalidArgumentException(sprintf('Rest interval must be zero or positive.'));
        }

        return new self($microseconds);
    }

    public static function fromMilliseconds(int $milliseconds): self
    {
        if ($milliseconds < 0) {
            throw new \InvalidArgumentException(sprintf('Rest interval must be zero or positive.'));
        }

        return new self($milliseconds * 1000);
    }

    public static function fromSeconds(int $seconds): self
    {
        if ($seconds < 0) {
            throw new \InvalidArgumentException(sprintf('Rest interval must be zero or positive.'));
        }

        return new self($seconds * 1000 * 1000);
    }
}
