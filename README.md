# Happy Inc Worker

```php
namespace HappyInc\Worker;

$worker = WorkerBuilder
    ::create(static function (WorkerTicked $context): void {
        // some daemon logic
        $context->stop('You can stop the worker and specify the reason.');
    })
    ->setTickInterval(2) // in seconds 
    ->addWorkerStartedListener(static function (WorkerStarted $event): void {
        // do something on start
    })
    ->addWorkerTickedListener(static function (WorkerTicked $context): void {
        // do something on every tick
        $context->stop('You can stop the worker and specify the reason.');
    })
    ->addWorkerStoppedListener(static function (WorkerStopped $context): void {
        // do something after worker stopped
    })
    ->setEventDispatcher($symfonyEventDispatcher) // optionally set an event dispatcher service
    ->setMemorySoftLimit(1024 * 1024) // when memory size hits this number of bytes, the worker will stop gracefully
    ->setStopSignalChannel('mailer') // allows to stop the worker from another process, see below
    ->build()
;

$stopped = $worker->run();
$stopped->tickedTimes;
$stopped->stopReason;

// send a stop signal to all workers, subscribed to the "mailer" channel
(new FileStopSignaller())->sendStopSignal('mailer');
```
