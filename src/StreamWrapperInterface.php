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
     * @param int $options
     *
     * @return bool
     */
    public function rmdir(string $path, int $options): bool;

    /**
     * @param int $cast_as
     *
     * @return resource
     */
    public function stream_cast(int $cast_as); // phpcs:ignore

    /**
     * @return mixed
     */
    public function stream_close(); // phpcs:ignore

    /**
     * @return bool
     */
    public function stream_eof(): bool; // phpcs:ignore

    /**
     * @return bool
     */
    public function stream_flush(): bool; // phpcs:ignore

    /**
     * @param mixed $operation
     *
     * @return bool
     */
    public function stream_lock($operation): bool; // phpcs:ignore

    /**
     * @param string $path
     * @param string $mode
     * @param int $options
     * @param string $opened_path
     *
     * @return bool
     */
    public function stream_open(string $path, string $mode, int $options, string &$opened_path): bool; // phpcs:ignore

    /**
     * @param int $count
     *
     * @return string
     */
    public function stream_read(int $count): string; // phpcs:ignore

    /**
     * @param int $offset
     * @param int $whence = SEEK_SET
     *
     * @return bool
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
     */
    public function stream_stat(): array; // phpcs:ignore

    /**
     * @return int
     */
    public function stream_tell(): int; // phpcs:ignore

    /**
     * @param string $data
     *
     * @return int
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
