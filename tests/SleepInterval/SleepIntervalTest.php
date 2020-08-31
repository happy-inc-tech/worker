<?php

declare(strict_types=1);

namespace HappyInc\Worker\SleepInterval;

use HappyInc\Worker\Sleep\SleepInterval;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \HappyInc\Worker\Sleep\SleepInterval
 *
 * @small
 */
final class SleepIntervalTest extends TestCase
{
    public function testFromMicroseconds(): void
    {
        $interval = SleepInterval::fromMicroseconds(1);

        $this->assertSame(1, $interval->microseconds);
    }

    public function testFromMilliseconds(): void
    {
        $interval = SleepInterval::fromMilliseconds(1);

        $this->assertSame(1000, $interval->microseconds);
    }

    public function testFromSeconds(): void
    {
        $interval = SleepInterval::fromSeconds(1);

        $this->assertSame(1_000_000, $interval->microseconds);
    }
}
