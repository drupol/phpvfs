<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phpvfs\Exporter\AttributeAscii;
use drupol\phpvfs\Utils\Path;

/**
 * Class Directory.
 */
class Directory extends FilesystemNode implements DirectoryInterface
{
    /**
     * @param string $id
     *
     * @return null|\drupol\phpvfs\Node\DirectoryInterface|\drupol\phpvfs\Node\FileInterface|\drupol\phpvfs\Node\FilesystemNodeInterface
     */
    public function containsAttributeId(string $id): ?FilesystemNodeInterface
    {
        /** @var \drupol\phpvfs\Node\FilesystemNodeInterface $child */
        foreach ($this->children() as $child) {
            if ($child->getAttribute('id') === $id) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @param string $id
     * @param array $attributes
     *
     * @throws \Exception
     *
     * @return \drupol\phpvfs\Node\Directory
     */
    public static function create(string $id, array $attributes = [])
    {
        $path = Path::fromString($id);

        if (\DIRECTORY_SEPARATOR !== $id && false !== \strpos($id, \DIRECTORY_SEPARATOR)) {
            if ($path->isAbsolute()) {
                $firstPart = \DIRECTORY_SEPARATOR;
            } else {
                $firstPart = $path->shift();
            }

            $root = self::create($firstPart, $attributes);

            foreach ($path->getIterator() as $pathPart) {
                $child = new self(['id' => $pathPart]);
                $root->add($child);
                $root = $child;
            }

            return $root;
        }

        $attributes = ['id' => $id] + $attributes;

        return new self($attributes);
    }

    /**
     * @param string $id
     *
     * @throws \Exception
     *
     * @return \drupol\phptree\Node\NodeInterface|\drupol\phpvfs\Node\DirectoryInterface
     */
    public function mkdir(string $id)
    {
        $dir = self::create($id);

        return $this->add($dir->root());
    }

    /**
     * @param string $id
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function rmdir(string $id)
    {
        if (!$this->exist($id)) {
            throw new \Exception(sprintf('Cannot remove %s: No such file or directory.', $id));
        }

        $path = Path::fromString($id);

        if ($path->isRoot()) {
            throw new \Exception(sprintf('Cannot remove root directory.'));
        }

        /** @var \drupol\phpvfs\Node\DirectoryInterface $cwd */
        $cwd = $path->isAbsolute() ?
            $this->root() :
            $this;

        $last = $path->getLastPart();

        foreach ($cwd->all() as $child) {
            if (!($child instanceof DirectoryInterface)) {
                continue;
            }

            if ($child->getAttribute('id') !== $last) {
                continue;
            }

            $cwd = $child->getParent();
            $cwd->remove($child);
        }

        return $cwd;
    }

    /**
     * @param \drupol\phpvfs\Filesystem\FilesystemInterface $vfs
     * @param string $id
     *
     *@throws \Exception
     *
     * @return \drupol\phpvfs\Node\FilesystemNodeInterface
     */
    public function get(string $id)
    {
        $path = Path::fromString($id);

        if ($path->isRoot()) {
            return $this->root();
        }

        /** @var \drupol\phpvfs\Node\DirectoryInterface $cwd */
        $child = $path->isAbsolute() ?
            $this->root() :
            $this;

        foreach ($path->getIterator() as $pathPart) {
            $child = $child->containsAttributeId($pathPart);
        }

        return $child;
    }

    /**
     * @param string ...$ids
     *
     * @return bool
     */
    public function exist(string ...$ids): bool
    {
        $exist = true;
        $existId = true;

        foreach ($ids as $id) {
            $path = Path::fromString($id);

            /** @var \drupol\phpvfs\Node\DirectoryInterface $cwd */
            $cwd = $path->isAbsolute() ?
                $this->root() :
                $this;

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
