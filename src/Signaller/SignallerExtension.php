<?php

declare(strict_types=1);

namespace HappyInc\Worker\Signaller;

use HappyInc\Worker\Context;
use HappyInc\Worker\Stop;
use HappyInc\Worker\WorkerJobDoneExtension;
use HappyInc\Worker\WorkerStartedExtension;

final class SignallerExtension implements WorkerStartedExtension, WorkerJobDoneExtension
{
    private string $channel;

    private Signaller $signaller;

    public function __construct(string $channel, ?Signaller $signaller = null)
    {
        $this->channel = $channel;
        $this->signaller = $signaller ?? new FileSignaller();
    }

    public function started(Context $context): void
    {
        /** @var SignallerContextData $data */
        $data = $context->dataOf(SignallerContextData::class);
        $data->listener = $this->signaller->createListener($this->channel);
    }

    public function jobDone(Context $context, int $jobIndex): ?Stop
    {
        /** @var SignallerContextData $data */
        $data = $context->dataOf(SignallerContextData::class);

        if (($data->listener)()) {
            return new Stop(sprintf('A stop signal was received from channel "%s".', $this->channel));
        }

        return null;
    }
}
