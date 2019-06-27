<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Commands;

use drupol\phpvfs\Filesystem\FilesystemInterface;
use drupol\phpvfs\Utils\Path;

class Cd
{
    /**
     * @param \drupol\phpvfs\Filesystem\FilesystemInterface $vfs
     * @param string $id
     */
    public static function exec(FilesystemInterface $vfs, string $id)
    {
        $path = Path::fromString($id);

        /** @var \drupol\phpvfs\Node\DirectoryInterface $cwd */
        $cwd = $path->isAbsolute() ?
            $vfs->getCwd()->root() :
            $vfs->getCwd();

        foreach ($path->getIterator() as $pathPart) {
            if (null !== $child = $cwd->containsAttributeId($pathPart)) {
                $cwd = $child;
            }
        }

        $vfs->setCwd($cwd);
    }
}
