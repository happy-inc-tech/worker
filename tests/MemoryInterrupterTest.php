<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @internal
 * @covers \HappyInc\Worker\MemoryInterrupter
 *
 * @small
 */
final class MemoryInterrupterTest extends TestCase
{
    public function testDoesNotStopWhenMemoryLimitIsNotReached(): void
    {
        $interrupter = new MemoryInterrupter(100, static function (): int { return 50; });
        $context = new Context(0, new NullLogger());

        $interrupter($context);

        $this->assertFalse($context->stopped);
    }

    public function testStopsWhenMemoryLimitReached(): void
    {
        $interrupter = new MemoryInterrupter(100, static function (): int { return 200; });
        $context = new Context(0, new NullLogger());

        $interrupter($context);

        $this->assertTrue($context->stopped);
    }
}
