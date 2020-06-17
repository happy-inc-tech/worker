<?php

declare(strict_types=1);

namespace HappyInc\Worker;

/**
 * @internal
 */
final class SigtermInterrupter
{
    /**
     * @var bool
     */
    private $interrupt = false;

    public function __construct()
    {
        pcntl_signal(SIGTERM, function (): void {
            $this->interrupt = true;
        });
    }

    public function __invoke(Context $context): void
    {
        pcntl_signal_dispatch();

        if (!$this->interrupt) {
            return;
        }

        $context->stop();
        $context->log('Worker stopped after receiving a SIGTERM signal.');
    }
}
