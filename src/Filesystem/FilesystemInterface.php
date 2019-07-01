<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Filesystem;

use drupol\phpvfs\Node\DirectoryInterface;

/**
 * Interface FilesystemInterface.
 */
interface FilesystemInterface
{
    /**
     * @return \drupol\phpvfs\Node\DirectoryInterface
     */
    public function getCwd(): DirectoryInterface;

    /**
     * @return string
     */
    public function pwd(): string;

    /**
     * @return \drupol\phpvfs\Node\DirectoryInterface
     */
    public function root(): DirectoryInterface;

    /**
     * @param \drupol\phpvfs\Node\DirectoryInterface $directory
     *
     * @return mixed
     */
    public function setCwd(DirectoryInterface $directory);
}
