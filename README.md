# Happy Inc Worker

```php
use HappyInc\Worker\MemoryLimit\MemoryLimit;
use HappyInc\Worker\MemoryLimit\MemoryLimitExtension;
use HappyInc\Worker\ProcessSignal\ProcessSignalExtension;
use HappyInc\Worker\Signaller\FileSignaller;
use HappyInc\Worker\Signaller\SignallerExtension;
use HappyInc\Worker\Sleep\SleepExtension;
use HappyInc\Worker\Sleep\SleepInterval;
use HappyInc\Worker\Worker;
use Psr\Log\LogLevel;

$signaller = new FileSignaller('/some/dir');

$worker = new Worker([
    new MemoryLimitExtension(
        MemoryLimit::fromIniMemoryLimit(0.7), // stop when allocated memory reaches 70% of php.ini memory_limit
        $systemLogger,
        LogLevel::CRITICAL, // optionally log when memory limit is reached with the specified level
    ),
    new ProcessSignalExtension([SIGINT]), // gracefully stop the worker when Ctrl+C is pressed in the terminal
    new SignallerExtension('mailing_worker', $signaller), // allows to send a stop signal from a different process
    new SleepExtension(SleepInterval::fromSeconds(1)), // sleep 1 second after each job
]);

$worker->workOn(function (): void {
    // some job
});

$signaller->sendSignal('mailing_worker'); // stop all workers, listening to the "mailing_worker" channel via the SignallerExtension
```
