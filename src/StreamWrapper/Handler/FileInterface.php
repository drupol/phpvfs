<?php

declare(strict_types = 1);

namespace drupol\phpvfs\StreamWrapper\Handler;

/**
 * Interface FileInterface.
 *
 * @internal
 */
interface FileInterface
{
    /**
     * @return \drupol\phpvfs\Node\FileInterface
     */
    public function getFile(): \drupol\phpvfs\Node\FileInterface;

    /**
     * @return string
     */
    public function getMode(): string;

    /**
     * @return int
     */
    public function getPosition(): int;

    /**
     * @return bool
     */
    public function isAppendable(): bool;

    /**
     * @return bool
     */
    public function isExtended(): bool;

    /**
     * @return bool
     */
    public function isReadable(): bool;

    /**
     * @return bool
     */
    public function isWritable(): bool;

    /**
     * @param int $bytes
     *
     * @return string
     */
    public function read(int $bytes): string;

    /**
     * @return mixed
     */
    public function seekToEnd();

    /**
     * @param int $bytes
     *
     * @return \drupol\phpvfs\StreamWrapper\Handler\File
     */
    public function setPosition(int $bytes): File;

    /**
     * @return int
     */
    public function size(): int;

    /**
     * @param int $bytes
     *
     * @return \drupol\phpvfs\StreamWrapper\Handler\File
     */
    public function truncate(int $bytes = 0): File;

    /**
     * @param string $data
     *
     * @return int
     */
    public function write(string $data): int;
}
