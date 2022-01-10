<?php

declare(strict_types=1);

namespace HappyInc\Worker\MemoryLimit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \HappyInc\Worker\MemoryLimit\MemoryLimit
 *
 * @small
 */
final class MemoryLimitTest extends TestCase
{
    public function testFromBytes(): void
    {
        $limit = MemoryLimit::fromBytes(16);

        $this->assertSame(16, $limit->bytes);
    }

    public function testFromMegaBytes(): void
    {
        $limit = MemoryLimit::fromMegabytes(1);

        $this->assertSame(1048576, $limit->bytes);
    }

    /**
     * @dataProvider fromIniMemoryLimitProvider
     */
    public function testFromIniMemoryLimit(string $iniLimit, float $share, int $expectedLimit): void
    {
        $originalIniLimit = ini_get('memory_limit');
        ini_set('memory_limit', $iniLimit);

        $limit = MemoryLimit::fromIniMemoryLimit($share);

        $this->assertSame($expectedLimit, $limit->bytes);

        ini_set('memory_limit', $originalIniLimit);
    }

    /**
     * @psalm-return \Generator<int, array{string, float, int}>
     */
    public function fromIniMemoryLimitProvider(): \Generator
    {
        yield ['-1', 1, 100 * 1024 ** 2];
        yield ['10485760', 1, 10485760];
        yield ['10485760', 0.1, 1048576];
        yield ['10240k', 1, 10 * 1024 ** 2];
        yield ['10240K', 1, 10 * 1024 ** 2];
        yield ['10m', 1, 10 * 1024 ** 2];
        yield ['10M', 1, 10 * 1024 ** 2];
        yield ['1g', 1, 1024 ** 3];
        yield ['1G', 1, 1024 ** 3];
    }
}
