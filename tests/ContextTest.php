<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \HappyInc\Worker\Context
 *
 * @small
 */
final class ContextTest extends TestCase
{
    public function testSameObjectReturned(): void
    {
        $context = new Context();

        $data = $context->dataOf(ContextDataStub::class);
        $data2 = $context->dataOf(ContextDataStub::class);

        $this->assertSame($data, $data2);
    }
}
