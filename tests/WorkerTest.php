<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \HappyInc\Worker\Worker
 *
 * @small
 */
final class WorkerTest extends TestCase
{
    public function testCallSequence(): void
    {
        $extension = new class() implements WorkerStartedExtension, WorkerJobDoneExtension, WorkerStoppedExtension {
            public array $callsSequence = [];

            public function started(Context $context): void
            {
                $this->callsSequence[] = 'started';
            }

            public function jobDone(Context $context, int $jobIndex): ?Stop
            {
                $this->callsSequence[] = 'jobDone';

                return new Stop();
            }

            public function stopped(Context $context, Result $result): void
            {
                $this->callsSequence[] = 'stopped';
            }
        };

        (new Worker([$extension]))->workOn(static function () use ($extension): void {
            $extension->callsSequence[] = 'job';
        });

        $this->assertSame(['started', 'job', 'jobDone', 'stopped'], $extension->callsSequence);
    }

    /**
     * @psalm-suppress InternalClass
     */
    public function testSameContextPassedAround(): void
    {
        $extension = new class() implements WorkerStartedExtension, WorkerJobDoneExtension, WorkerStoppedExtension {
            public ?Context $context = null;

            public function started(Context $context): void
            {
                $this->context = $context;
            }

            public function jobDone(Context $context, int $jobIndex): ?Stop
            {
                WorkerTest::assertSame($this->context, $context);

                return new Stop();
            }

            public function stopped(Context $context, Result $result): void
            {
                WorkerTest::assertSame($this->context, $context);
            }
        };

        (new Worker([$extension]))->workOn(static function (Context $context) use ($extension): void {
            self::assertSame($extension->context, $context);
        });
    }
}
