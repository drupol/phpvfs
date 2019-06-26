<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Commands;

use drupol\phpvfs\Filesystem\Vfs;
use drupol\phpvfs\Node\File;

class Touch
{
    public static function exec(Vfs $vfs, string $id, string $content = null, array $attributes = [])
    {
        $file = File::create($id, '');

        $vfs->getCwd()->add($file);
    }
}
