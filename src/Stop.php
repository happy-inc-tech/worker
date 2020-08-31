<?php

declare(strict_types=1);

namespace HappyInc\Worker;

/**
 * @psalm-immutable
 */
final class Stop
{
    public ?string $reason;

    public function __construct(?string $reason = null)
    {
        $this->reason = $reason;
    }
}
