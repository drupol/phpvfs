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
     * @param string $content
     * @param array $attributes
     *
     * @throws \Exception
     *
     * @return \drupol\phpvfs\Node\File
     */
    public static function exec(FilesystemInterface $vfs, string $id, string $content = '', array $attributes = [])
    {
        if (Exist::exec($vfs, $id)) {
            throw new \Exception('File already exist.');
        }

        $file = File::create($id, $content, $attributes);

        $vfs->getCwd()->add($file);

        return $file;
    }
}
