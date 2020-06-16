<?php

declare(strict_types=1);

namespace HappyInc\Worker;

use Psr\Log\LogLevel;

final class FileStopSignaller
{
    private const DEFAULT_LOG_MESSAGE = 'Worker stopped after receiving a stop signal from channel {channel}.';

    /**
     * @var string
     */
    private $dir;

    public function __construct(?string $dir = null)
    {
        $this->dir = rtrim($dir ?? sys_get_temp_dir(), \DIRECTORY_SEPARATOR);
    }

    public function sendStopSignal(string $channel): void
    {
        $this->fileContents($channel, true);
    }

    /**
     * @psalm-return callable(Context): void
     */
    public function createInterrupter(string $channel, string $logMessage = self::DEFAULT_LOG_MESSAGE, array $logContext = [], string $logLevel = LogLevel::INFO): callable
    {
        $value = $this->fileContents($channel);

        return function (Context $context) use ($channel, $value, $logMessage, $logContext, $logLevel): void {
            if ($value !== $this->fileContents($channel)) {
                $context->stop();
                $logContext['channel'] = $channel;
                $context->log($logMessage, $logContext, $logLevel);
            }
        };
    }

    private function fileContents(string $channel, bool $forceUpdate = false): string
    {
        $file = sprintf('%s/%s.wsc', $this->dir, sha1($channel));

        if (!$forceUpdate && false !== $value = @file_get_contents($file)) {
            return $value;
        }

        if (!is_dir($this->dir) && !@mkdir($this->dir, 0777, true)) {
            throw new \RuntimeException(sprintf('Failed to create directory "%s".', $this->dir));
        }

        $value = (string) time();

        if (!@file_put_contents($file, $value)) {
            throw new \RuntimeException(sprintf('Failed to write to "%s".', $file));
        }

        return $value;
    }
}
