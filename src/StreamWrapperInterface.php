<?php

declare(strict_types = 1);

namespace drupol\phpvfs;

/**
 * Interface StreamWrapperInterface.
 */
interface StreamWrapperInterface
{
    /**
     * @return bool
     */
    public function dir_closedir(): bool; // phpcs:ignore

    /**
     * @param string $path
     * @param int $options
     *
     * @return bool
     */
    public function dir_opendir(string $path, int $options): bool; // phpcs:ignore

    /**
     * @return string
     */
    public function dir_readdir(): string; // phpcs:ignore

    /**
     * @return bool
     */
    public function dir_rewinddir(): bool; // phpcs:ignore

    /**
     * @param string $path
     * @param int $mode
     * @param int $options
     *
     * @return bool
     */
    public function mkdir(string $path, int $mode, int $options): bool;

    /**
     * @param string $path_from
     * @param string $path_to
     *
     * @return bool
     */
    public function rename(string $path_from, string $path_to): bool;

    /**
     * @param string $path
     *   The directory URL which should be removed.
     * @param int $options
     *   A bitwise mask of values, such as STREAM_MKDIR_RECURSIVE.
     *
     * @return bool
     *   Returns TRUE on success or FALSE on failure.
     *
     * @see http://php.net/streamwrapper.rmdir
     */
    public function rmdir(string $path, int $options): bool;

    /**
     * @param int $cast_as
     *
     * @return false|resource
     *
     * @see http://php.net/streamwrapper.stream-cast
     */
    public function stream_cast(int $cast_as); // phpcs:ignore

    /**
     * @see http://php.net/streamwrapper.stream-close
     */
    public function stream_close(): void; // phpcs:ignore

    /**
     * @return bool
     *
     * @see http://php.net/streamwrapper.stream-eof
     */
    public function stream_eof(): bool; // phpcs:ignore

    /**
     * @return bool
     *
     * @see http://php.net/streamwrapper.stream-flush
     */
    public function stream_flush(): bool; // phpcs:ignore

    /**
     * @param mixed $operation
     *
     * @return bool
     *
     * @see http://php.net/streamwrapper.stream-lock
     */
    public function stream_lock($operation): bool; // phpcs:ignore

    /**
     * @param string $path
     * @param string $mode
     * @param int $options
     * @param string $opened_path
     *
     * @return bool
     *
     * @see http://php.net/streamwrapper.stream-open
     */
    public function stream_open(string $path, string $mode, int $options, string &$opened_path): bool; // phpcs:ignore

    /**
     * @param int $count
     *
     * @return false|string
     *
     * @see http://php.net/streamwrapper.stream-read
     */
    public function stream_read(int $count); // phpcs:ignore

    /**
     * @param int $offset
     * @param int $whence = SEEK_SET
     *
     * @return bool
     *
     * @see http://php.net/streamwrapper.stream-seek
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool; // phpcs:ignore

    /**
     * @param int $option
     * @param int $arg1
     * @param int $arg2
     *
     * @return bool
     */
    public function stream_set_option(int $option, int $arg1, int $arg2): bool; // phpcs:ignore

    /**
     * @return array
     *
     * @see http://php.net/streamwrapper.stream-stat
     */
    public function stream_stat(): array; // phpcs:ignore

    /**
     * @return int
     *
     * @see http://php.net/streamwrapper.stream-tell
     */
    public function stream_tell(): int; // phpcs:ignore

    /**
     * @param string $data
     *
     * @return int
     *
     * @see http://php.net/streamwrapper.stream-write
     */
    public function stream_write(string $data): int; // phpcs:ignore

    /**
     * @param string $path
     *
     * @return bool
     */
    public function unlink(string $path): bool;

    /**
     * @param string $path
     * @param int $flags
     *
     * @return array
     */
    public function url_stat(string $path, int $flags): array; // phpcs:ignore
}
