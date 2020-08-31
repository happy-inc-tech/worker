<?php

declare(strict_types=1);

namespace HappyInc\Worker\ProcessSignal;

use HappyInc\Worker\Context;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \HappyInc\Worker\ProcessSignal\ProcessSignalExtension
 *
 * @small
 */
final class ProcessSignalExtensionTest extends TestCase
{
    public function testNotStoppedWhenDifferentSignalSent(): void
    {
        $context = new Context();
        $extension = new ProcessSignalExtension([SIGINT]);
        pcntl_signal(SIGUSR1, SIG_IGN);

        $extension->started($context);
        posix_kill(posix_getpid(), SIGUSR1);
        $stop = $extension->jobDone($context, 0);

        $this->assertNull($stop);
    }

    public function testStoppedWhenSignalSent(): void
    {
        $context = new Context();
        $extension = new ProcessSignalExtension([SIGINT]);

        $extension->started($context);
        posix_kill(posix_getpid(), SIGINT);
        $stop = $extension->jobDone($context, 0);

        $this->assertNotNull($stop);
    }
}
