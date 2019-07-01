<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

/**
 * Interface FileInterface.
 */
interface FileInterface extends FilesystemNodeInterface
{
    /**
     * @return int
     */
    public function getPosition(): int;

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
     * @param int $position
     *
     * @return \drupol\phpvfs\Node\FileInterface
     */
    public function setPosition(int $position): FileInterface;

    /**
     * @param int $bytes
     *
     * @return mixed
     */
    public function truncate(int $bytes = 0);

    /**
     * @param string $data
     *
     * @return int
     */
    public function write(string $data): int;
}
