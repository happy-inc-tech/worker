<?php

declare(strict_types=1);

namespace HappyInc\Worker;

interface WorkerStoppedExtension
{
    public function stopped(Context $context, Result $result): void;
}
