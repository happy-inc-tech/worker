<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \HappyInc\Worker\StopOnMemorySoftLimitExceeded
 *
 * @small
 */
final class StopOnMemorySoftLimitExceededTest extends TestCase
{
    public function testDoesNotStopWhenMemoryLimitIsNotReached(): void
    {
        $interrupter = new StopOnMemorySoftLimitExceeded(100, static function (): int { return 50; });
        $event = new WorkerTicked(0);

        $interrupter($event);

        $this->assertFalse($event->stopped);
    }

    public function testStopsWhenMemoryLimitReached(): void
    {
        $interrupter = new StopOnMemorySoftLimitExceeded(100, static function (): int { return 200; });
        $event = new WorkerTicked(0);

        $interrupter($event);

        $this->assertTrue($event->stopped);
    }
}
