# Happy Inc Worker

```php
use HappyInc\Worker\Worker;
use HappyInc\Worker\MemoryInterrupter;
use HappyInc\Worker\StopSignaller;
use HappyInc\Worker\Context;
use Psr\Log\LogLevel;

$stopSignaller = new StopSignaller();

$worker = new Worker(
    // an iterable of operations that should be executed on each tick
    [
        function (Context $context): void {
            $mailer->sendPendingEmails();
    
            if (someStopCondition()) {
                $context->stop();
                $context->log(
                    'Worker was stopped after tick {tick}.', 
                    ['tick' => $context->tick],
                    LogLevel::WARNING
                );
            }
        },
        new MemoryInterrupter(16 * 1024 * 1024), // 16mb memory limit
        $stopSignaller->createInterrupter('mail_worker'),
    ],
    // a logger, defaults to NullLogger
    $logger,
    // sleep interval between the ticks in seconds, defaults to 1
    5
);

$worker->run(); // or simply $worker(), since worker is a callable object
```

```php
// somewhere within deployment pipeline

use HappyInc\Worker\StopSignaller;

(new StopSignaller())->sendStopSignal('mail_worker');
```
