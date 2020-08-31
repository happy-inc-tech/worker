<?php

declare(strict_types=1);

namespace HappyInc\Worker\Signaller;

final class FileSignaller implements Signaller
{
    private const MAX_LENGTH = 26;

    private string $dir;

    public function __construct(?string $dir = null)
    {
        $this->dir = rtrim($dir ?? sys_get_temp_dir(), \DIRECTORY_SEPARATOR);
    }

    public function sendSignal(string $channel): void
    {
        if (!is_dir($this->dir) && !@mkdir($this->dir, 0777, true)) {
            throw new \RuntimeException(sprintf('Failed to create directory "%s".', $this->dir));
        }

        $file = $this->channelFile($channel);

        if (false === $handle = @fopen($this->channelFile($channel), 'cb')) {
            throw new \RuntimeException(sprintf('Failed to open file "%s" for writing.', $file));
        }

        if (!flock($handle, LOCK_EX)) {
            throw new \RuntimeException(sprintf('Failed to acquire an exclusive lock for file "%s".', $file));
        }

        ftruncate($handle, 0);
        fwrite($handle, (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s.u'));
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);
    }

    public function createListener(string $channel): callable
    {
        $file = $this->channelFile($channel);
        $initialValue = @file_get_contents($file, false, null, 0, self::MAX_LENGTH);

        return static fn (): bool => $initialValue !== @file_get_contents($file, false, null, 0, self::MAX_LENGTH);
    }

    private function channelFile(string $channel): string
    {
        return sprintf('%s/%s.worker_stop_signaller_channel.tmp', $this->dir, md5($channel));
    }
}
