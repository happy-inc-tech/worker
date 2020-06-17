<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @internal
 * @covers \HappyInc\Worker\StopSignaller
 *
 * @small
 */
final class StopSignallerTest extends TestCase
{
    public function testNotStoppedWithoutASignal(): void
    {
        $channel = 'channel';
        $signaller = new StopSignaller();
        $context = new Context($this->createMock(LoggerInterface::class));
        $interrupter = $signaller->createInterrupter($channel);

        $interrupter($context);

        $this->assertFalse($context->isStopped());
    }

    public function testNotStoppedWhenCreatedAfterASignal(): void
    {
        $channel = 'channel';
        $signaller = new StopSignaller();
        $signaller->sendStopSignal($channel);
        $context = new Context($this->createMock(LoggerInterface::class));
        $interrupter = $signaller->createInterrupter($channel);

        $interrupter($context);

        $this->assertFalse($context->isStopped());
    }

    public function testStoppedAfterASignal(): void
    {
        $channel = 'channel';
        $signaller = new StopSignaller();
        $context = new Context($this->createMock(LoggerInterface::class));
        $interrupter = $signaller->createInterrupter($channel);

        $signaller->sendStopSignal($channel);
        $interrupter($context);

        $this->assertTrue($context->isStopped());
    }
}
