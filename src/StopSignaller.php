<?php

declare(strict_types=1);

namespace HappyInc\Worker;

interface StopSignaller
{
    public function sendStopSignal(string $channel): void;

    /**
     * @psalm-return callable(WorkerTicked): void
     */
    public function createListener(string $channel): callable;
}
