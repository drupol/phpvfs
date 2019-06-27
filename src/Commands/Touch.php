<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Commands;

use drupol\phpvfs\Filesystem\FilesystemInterface;
use drupol\phpvfs\Node\File;

class Touch
{
    /**
     * @param \drupol\phpvfs\Filesystem\FilesystemInterface $vfs
     * @param string $id
     * @param null|string $content
     * @param array $attributes
     *
     * @throws \Exception
     */
    public static function exec(FilesystemInterface $vfs, string $id, string $content = null, array $attributes = [])
    {
        $vfs->getCwd()->add(File::create($id, $content, $attributes));
    }
}
