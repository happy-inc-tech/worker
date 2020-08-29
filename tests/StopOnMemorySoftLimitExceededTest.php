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
        $limit = memory_get_usage(true) + 1024;
        $interrupter = new StopOnMemorySoftLimitExceeded($limit);
        $event = new WorkerDoneJob(0);

        $interrupter($event);

        $this->assertFalse($event->stopped);
    }

    public function testStopsWhenMemoryLimitReached(): void
    {
        $limit = memory_get_usage() - 1024;
        $interrupter = new StopOnMemorySoftLimitExceeded($limit);
        $event = new WorkerDoneJob(0);

        $interrupter($event);

        $this->assertTrue($event->stopped);
    }
}
