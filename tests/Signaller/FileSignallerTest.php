<?php

declare(strict_types=1);

namespace HappyInc\Worker\Signaller;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \HappyInc\Worker\Signaller\FileSignaller
 *
 * @small
 */
final class FileSignallerTest extends TestCase
{
    public function testNoSignalReceivedWhenNotSent(): void
    {
        $channel = 'channel';
        $signaller = new FileSignaller();
        $listener = $signaller->createListener($channel);

        $signal = $listener();

        $this->assertFalse($signal);
    }

    public function testNoSignalReceivedWhenSentBeforeCreatingListener(): void
    {
        $channel = 'channel';
        $signaller = new FileSignaller();

        $signaller->sendSignal($channel);
        $signal = $signaller->createListener($channel)();

        $this->assertFalse($signal);
    }

    public function testSignalReceivedWhenSent(): void
    {
        $channel = 'channel';
        $signaller = new FileSignaller();
        $listener = $signaller->createListener($channel);

        $signaller->sendSignal($channel);
        $signal = $listener();

        $this->assertTrue($signal);
    }
}
