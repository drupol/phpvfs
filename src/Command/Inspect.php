<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Command;

use drupol\phpvfs\Filesystem\FilesystemInterface;
use drupol\phpvfs\Utils\Path;

class Inspect
{
    /**
     * @param \drupol\phpvfs\Filesystem\FilesystemInterface $vfs
     * @param string $id
     *
     * @throws \Exception
     *
     * @return string
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
            } else {
                throw new \Exception('Unknown path.');
            }
        }

        if (null === $cwd) {
            throw new \Exception('TODO');
        }

        return \get_class($cwd);
    }
}
