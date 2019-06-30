<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Command;

use drupol\phpvfs\Filesystem\FilesystemInterface;
use drupol\phpvfs\Utils\Path;

class Exist implements CommandInterface
{
    /**
     * @param \drupol\phpvfs\Filesystem\FilesystemInterface $vfs
     * @param string ...$ids
     *
     * @return bool
     */
    public static function exec(FilesystemInterface $vfs, string ...$ids): bool
    {
        $exist = true;
        $existId = true;

        foreach ($ids as $id) {
            $path = Path::fromString($id);

            /** @var \drupol\phpvfs\Node\DirectoryInterface $cwd */
            $cwd = $path->isAbsolute() ?
                $vfs->getCwd()->root() :
                $vfs->getCwd();

            foreach ($path->getIterator() as $pathPart) {
                $pathPartExist = false;

                if (\DIRECTORY_SEPARATOR === $pathPart) {
                    $pathPartExist = true;
                } elseif (null !== $child = $cwd->containsAttributeId($pathPart)) {
                    $pathPartExist = true;
                    $cwd = $child;
                }

                $existId = $existId && $pathPartExist;
            }

            $exist = $exist && $existId;
        }

        return $exist;
    }
}
