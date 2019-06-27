<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Commands;

use drupol\phpvfs\Filesystem\FilesystemInterface;
use drupol\phpvfs\Node\File;

class Touch
{
    public static function exec(FilesystemInterface $vfs, string $id, string $content = null, array $attributes = [])
    {
        $file = File::create($id, '');

        $vfs->getCwd()->add($file);
    }
}
