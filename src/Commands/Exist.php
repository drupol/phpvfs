<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Commands;

use drupol\phpvfs\Filesystem\FilesystemInterface;
use drupol\phpvfs\Utils\Path;

class Exist
{
    public static function exec(FilesystemInterface $vfs, string ...$ids)
    {
        $exist = true;

        foreach ($ids as $id) {
            $existId = true;
            $path = Path::fromString($id);

            /** @var \drupol\phpvfs\Node\DirectoryInterface $cwd */
            $cwd = $path->isAbsolute() ?
                $vfs->getCwd()->root() :
                $vfs->getCwd();

            foreach ($path->getIterator() as $pathPart) {
                if ($pathPart === DIRECTORY_SEPARATOR) {
                  $existId = true;
                } elseif (null !== $child = $cwd->containsAttributeId($pathPart)) {
                    $existId = true;
                    $cwd = $child;
                } else {
                    $existId = false;
                }
            }

            $exist = $exist && $existId;
        }

        return $exist;
    }
}
