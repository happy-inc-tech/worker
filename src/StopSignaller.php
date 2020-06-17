<?php

declare(strict_types=1);

namespace HappyInc\Worker;

final class StopSignaller
{
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
     * @psalm-param ?callable(Context): void $onStopped
     * @psalm-return callable(Context): void
     */
    public function createInterrupter(string $channel, ?callable $onStopped = null): callable
    {
        $value = $this->fileContents($channel);

        return function (Context $context) use ($channel, $value, $onStopped): void {
            if ($value === $this->fileContents($channel)) {
                return;
            }

            $context->stop();
            $context->log('Worker stopped after receiving a stop signal from channel {channel}.', ['channel' => $channel]);

            if (null !== $onStopped) {
                $onStopped($context);
            }
        };
    }

    private function fileContents(string $channel, bool $forceUpdate = false): string
    {
        $file = sprintf('%s/%s.channel.tmp', $this->dir, sha1($channel));

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
