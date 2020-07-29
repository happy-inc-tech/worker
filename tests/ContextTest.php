<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

/**
 * @internal
 * @covers \HappyInc\Worker\Context
 *
 * @small
 */
final class ContextTest extends TestCase
{
    public function testInitiallyNotStopped(): void
    {
        $context = new Context(0, new NullLogger());

        $this->assertFalse($context->stopped);
    }

    public function testStop(): void
    {
        $context = new Context(0, new NullLogger());

        $context->stop();

        $this->assertTrue($context->stopped);
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
