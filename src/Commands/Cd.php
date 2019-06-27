<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Commands;

use drupol\phpvfs\Filesystem\Vfs;
use drupol\phpvfs\Utils\Path;

class Cd
{
    public static function exec(Vfs $vfs, string $id)
    {
        $path = Path::fromString($id);

        $cwd = $path->isAbsolute() ?
            $vfs->getCwd()->root() :
            $vfs->getCwd();

        foreach ($path->getIterator() as $pathPart) {
            if (false !== $child = $cwd->containsAttributeId($pathPart)) {
                $cwd = $child;
            }
        }

        $vfs->setCwd($cwd);
    }
}
