<?php

declare(strict_types=1);

namespace HappyInc\Worker\Signaller;

use HappyInc\Worker\ContextData;

/**
 * @internal
 * @psalm-internal \HappyInc\Worker\Signaller
 */
final class SignallerContextData implements ContextData
{
    /**
     * @var callable
     * @psalm-var callable(): bool
     */
    public $listener;

    public function __construct()
    {
        $this->listener = static fn (): bool => false;
    }
}
