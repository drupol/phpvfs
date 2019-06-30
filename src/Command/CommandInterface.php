<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Command;

use drupol\phpvfs\Filesystem\FilesystemInterface;

/**
 * Interface CommandInterface.
 */
interface CommandInterface
{
    /**
     * @param FilesystemInterface $filesystem
     *
     * @return mixed
     */
    public static function Exec(FilesystemInterface $filesystem);
}
