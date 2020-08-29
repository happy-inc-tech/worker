<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \HappyInc\Worker\FileStopSignaller
 *
 * @small
 */
final class FileStopSignallerTest extends TestCase
{
    public function testNotStoppedWithoutASignal(): void
    {
        $channel = 'channel';
        $signaller = new FileStopSignaller();
        $event = new WorkerDoneJob(0);
        $interrupter = $signaller->createListener($channel);

        $interrupter($event);

        $this->assertFalse($event->stopped);
    }

    public function testNotStoppedWhenCreatedAfterASignal(): void
    {
        $channel = 'channel';
        $signaller = new FileStopSignaller();
        $signaller->sendStopSignal($channel);
        $event = new WorkerDoneJob(0);
        $interrupter = $signaller->createListener($channel);

        $interrupter($event);

        $this->assertFalse($event->stopped);
    }

    public function testStoppedAfterASignal(): void
    {
        $channel = 'channel';
        $signaller = new FileStopSignaller();
        $event = new WorkerDoneJob(0);
        $interrupter = $signaller->createListener($channel);

        $signaller->sendStopSignal($channel);
        $interrupter($event);

        $this->assertTrue($event->stopped);
    }
}
