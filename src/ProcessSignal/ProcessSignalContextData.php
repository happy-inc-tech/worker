<?php

declare(strict_types=1);

namespace HappyInc\Worker\ProcessSignal;

use HappyInc\Worker\ContextData;

/**
 * @internal
 * @psalm-internal \HappyInc\Worker\ProcessSignal
 */
final class ProcessSignalContextData implements ContextData
{
    public ?int $receivedSignal = null;

    public function __construct()
    {
    }
}
