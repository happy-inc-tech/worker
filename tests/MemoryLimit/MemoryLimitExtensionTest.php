<?php

declare(strict_types=1);

namespace HappyInc\Worker\MemoryLimit;

use HappyInc\Worker\Context;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \HappyInc\Worker\MemoryLimit\MemoryLimitExtension
 *
 * @small
 */
final class MemoryLimitExtensionTest extends TestCase
{
    public function testNotStoppedWhenMemoryLimitNotReached(): void
    {
        $context = new Context();
        $limit = MemoryLimit::fromBytes(memory_get_usage(true) + 1024);
        $extension = new MemoryLimitExtension($limit);

        $stop = $extension->jobDone($context, 0);

        $this->assertNull($stop);
    }

    public function testStoppedWhenMemoryLimitReached(): void
    {
        $context = new Context();
        $limit = MemoryLimit::fromBytes(memory_get_usage() - 1024);
        $extension = new MemoryLimitExtension($limit);

        $stop = $extension->jobDone($context, 0);

        $this->assertNotNull($stop);
    }
}
