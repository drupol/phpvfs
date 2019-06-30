<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Command;

use drupol\phpvfs\Filesystem\FilesystemInterface;
use drupol\phpvfs\Node\DirectoryInterface;
use drupol\phpvfs\Utils\Path;

class Cd
{
    /**
     * @param \drupol\phpvfs\Filesystem\FilesystemInterface $vfs
     * @param string $id
     *
     * @throws \Exception
     *
     * @return \drupol\phpvfs\Node\DirectoryInterface
     */
    public static function exec(FilesystemInterface $vfs, string $id): DirectoryInterface
    {
        $path = Path::fromString($id);

        /** @var \drupol\phpvfs\Node\DirectoryInterface $cwd */
        $cwd = $path->isAbsolute() ?
            $vfs->getCwd()->root() :
            $vfs->getCwd();

        if ($path->isRoot()) {
            $vfs->setCwd($cwd);

            return $cwd;
        }

        foreach ($path->getIterator() as $pathPart) {
            if (null !== $child = $cwd->containsAttributeId($pathPart)) {
                $cwd = $child;

                continue;
            }

            throw new \Exception('Unknown directory.');
        }

        $vfs->setCwd($cwd);

        return $cwd;
    }
}
