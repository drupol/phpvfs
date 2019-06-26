<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Commands;

use drupol\phpvfs\Filesystem\Vfs;

class Cd
{
    public static function exec(Vfs $vfs, string $id)
    {
        $cwd = $vfs->getCwd();

        if (0 === \strpos($id, \DIRECTORY_SEPARATOR, 0)) {
            $cwd = $cwd->root();
        }

        $id = \trim($id, '/');

        $paths = \explode('/', $id);

        foreach ($paths as $path) {
            if (false !== $child = $cwd->containsAttributeId($path)) {
                $cwd = $child;
            }
        }

        $vfs->setCwd($cwd);
    }
}
