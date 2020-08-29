<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \HappyInc\Worker\RestInterval
 *
 * @small
 */
final class RestIntervalTest extends TestCase
{
    public function testFromMicroseconds(): void
    {
        $interval = RestInterval::fromMicroseconds(1);

        $this->assertSame(1, $interval->microseconds);
    }

    public function testFromMilliseconds(): void
    {
        $interval = RestInterval::fromMilliseconds(1);

        $this->assertSame(1000, $interval->microseconds);
    }

    public function testFromSeconds(): void
    {
        $interval = RestInterval::fromSeconds(1);

        $this->assertSame(1000000, $interval->microseconds);
    }
}
