<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

/**
 * Interface FileInterface.
 */
interface FileInterface extends FilesystemNodeInterface
{
    /**
     * @return string
     */
    public function read(): string;

    /**
     * @param string $data
     *
     * @return \drupol\phpvfs\Node\FileInterface
     */
    public function write(string $data): FileInterface;
}
