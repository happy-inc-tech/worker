<?php

declare(strict_types=1);

namespace HappyInc\Worker\Signaller;

interface Signaller
{
    public function sendSignal(string $channel): void;

    /**
     * @psalm-return callable(): bool
     */
    public function createListener(string $channel): callable;
}
