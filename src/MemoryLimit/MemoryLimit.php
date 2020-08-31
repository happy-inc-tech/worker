<?php

declare(strict_types=1);

namespace HappyInc\Worker\MemoryLimit;

/**
 * @psalm-immutable
 */
final class MemoryLimit
{
    public int $bytes;

    private function __construct(int $bytes)
    {
        $this->bytes = $bytes;
    }

    /**
     * @psalm-pure
     */
    public static function fromBytes(int $bytes): self
    {
        if ($bytes <= 0) {
            throw new \InvalidArgumentException(sprintf('Memory limit must be a positive number, got %d bytes.', $bytes));
        }

        return new self($bytes);
    }

    /**
     * @psalm-pure
     */
    public static function fromMegabytes(int $megabytes): self
    {
        if ($megabytes <= 0) {
            throw new \InvalidArgumentException(sprintf('Memory limit must be a positive number, got %d megabytes.', $megabytes));
        }

        return new self($megabytes * 1024 ** 2);
    }

    public static function fromIniMemoryLimit(float $share): self
    {
        if (!($share > 0 && $share <= 1)) {
            throw new \InvalidArgumentException(sprintf('Share must be greater than 0 and less than or equal to 1, got %g.', $share));
        }

        $iniLimit = self::parseMemory(ini_get('memory_limit'));

        if ($iniLimit <= 0) {
            return self::fromMegabytes(100);
        }

        return new self((int) ($iniLimit * $share));
    }

    /**
     * @psalm-pure
     */
    private static function parseMemory(string $memory): int
    {
        if (!preg_match('/^(-?\d+)([tgmk])?$/i', trim($memory), $matches)) {
            throw new \InvalidArgumentException(sprintf('Unknown memory string format "%s".', $memory));
        }

        $value = (int) $matches[1];

        if (!isset($matches[2])) {
            return $value;
        }

        switch (strtolower($matches[2])) {
            case 't':
                $value *= 1024;
                // no break
            case 'g':
                $value *= 1024;
                // no break
            case 'm':
                $value *= 1024;
                // no break
            case 'k':
                $value *= 1024;
        }

        return $value;
    }
}
