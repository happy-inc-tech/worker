# Happy Inc Worker

```php
namespace HappyInc\Worker;

$worker = WorkerBuilder
    ::create()
    ->setRestIntervalSeconds(2) 
    ->addWorkerStartedListener(static function (WorkerStarted $event): void {
        // do something on start
    })
    ->addWorkerDoneJobListener(static function (WorkerDoneJob $event): void {
        // do something every iteration
        $event->stop('You can stop the worker and specify the reason.');
    })
    ->addWorkerStoppedListener(static function (WorkerStopped $event): void {
        // do something after worker stopped
    })
    ->setEventDispatcher($symfonyEventDispatcher) // optionally set an event dispatcher service
    ->setMemorySoftLimitBytes(1024 * 1024) // when allocated memory size hits this number of bytes, the worker will stop gracefully
    ->setStopSignalChannel('mailer') // stop the worker with a signal from another process, see below
    ->build()
;

$stopped = $worker->do(static function (WorkerDoneJob $event): void {
    // some job logic
    $event->stop('You can stop the worker and specify the reason.');
});
$stopped->jobs;
$stopped->stopReason;

// send a stop signal to all workers, subscribed to the "mailer" channel
(new FileStopSignaller())->sendStopSignal('mailer');
```
