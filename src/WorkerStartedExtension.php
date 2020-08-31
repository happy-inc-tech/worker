<?php

declare(strict_types=1);

namespace HappyInc\Worker;

interface WorkerStartedExtension
{
    public function started(Context $context): void;
}
