<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @internal
 * @covers \HappyInc\Worker\Context
 *
 * @small
 */
final class ContextTest extends TestCase
{
    public function testGetTick(): void
    {
        $tick = 109;

        $context = new Context($tick, $this->createMock(LoggerInterface::class));

        $this->assertSame($tick, $context->getTick());
    }

    public function testInitiallyNotStopped(): void
    {
        $context = new Context(0, $this->createMock(LoggerInterface::class));

        $this->assertFalse($context->isStopped());
    }

    public function testStop(): void
    {
        $context = new Context(0, $this->createMock(LoggerInterface::class));

        $context->stop();

        $this->assertTrue($context->isStopped());
    }

    public function testLog(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('log')
            ->with(LogLevel::CRITICAL, 'message', ['a' => 'b'])
        ;
        $context = new Context(0, $logger);

        $context->log('message', ['a' => 'b'], LogLevel::CRITICAL);
    }
}
