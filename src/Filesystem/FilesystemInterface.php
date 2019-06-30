<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Filesystem;

use drupol\phpvfs\Node\DirectoryInterface;
use drupol\phpvfs\Node\FilesystemNodeInterface;

/**
 * Interface FilesystemInterface.
 */
interface FilesystemInterface
{
    /**
     * @param string $id
     *
     * @return bool
     */
    public function exist(string $id): bool;

    /**
     * @param string $id
     *
     * @return null|\drupol\phpvfs\Node\FilesystemNodeInterface
     */
    public function get(string $id): ?FilesystemNodeInterface;

    /**
     * @return \drupol\phpvfs\Node\DirectoryInterface
     */
    public function getCwd(): DirectoryInterface;

    /**
     * @param \drupol\phpvfs\Node\DirectoryInterface $directory
     *
     * @return mixed
     */
    public function setCwd(DirectoryInterface $directory);
}
