<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phpvfs\Utils\Path;

/**
 * Class Directory.
 */
class Directory extends FilesystemNode implements DirectoryInterface
{
    /**
     * @param string $id
     *
     * @throws \Exception
     *
     * @return \drupol\phpvfs\Node\DirectoryInterface
     */
    public function cd(string $id): DirectoryInterface
    {
        if (!$this->exist($id)) {
            throw new \Exception(\sprintf('Cannot change directory to %s: No such file or directory.', $id));
        }

        $cwd = $this->get($id);

        if ($cwd instanceof DirectoryInterface) {
            return $cwd;
        }
    }

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

    /**
     * @param string $id
     *
     * @throws \Exception
     *
     * @return \drupol\phpvfs\Node\FilesystemNodeInterface
     */
    public function get(string $id): FilesystemNodeInterface
    {
        if (!$this->exist($id)) {
            throw new \Exception(\sprintf('Unable to get %s', $id));
        }

        $path = Path::fromString($id);

        if ((($root = $this->root()) instanceof DirectoryInterface) && $path->isRoot()) {
            return $root;
        }

        /** @var \drupol\phpvfs\Node\DirectoryInterface $cwd */
        $cwd = $path->isAbsolute() ?
            $this->root() :
            $this;

        foreach ($path->getIterator() as $pathPart) {
            $cwd = $cwd->containsAttributeId($pathPart);
        }

        return $cwd;
    }

    /**
     * @param string $id
     *
     * @throws \Exception
     *
     * @return \drupol\phpvfs\Node\DirectoryInterface
     */
    public function mkdir(string $id): DirectoryInterface
    {
        $dir = self::create($id);

        $dir = $this->add($dir->root());

        if ($dir instanceof DirectoryInterface) {
            return $dir;
        }
    }

    /**
     * @param string $id
     *
     * @throws \Exception
     *
     * @return \drupol\phpvfs\Node\DirectoryInterface
     */
    public function rmdir(string $id): DirectoryInterface
    {
        if (!$this->exist($id)) {
            throw new \Exception(\sprintf('Cannot remove %s: No such file or directory.', $id));
        }

        $path = Path::fromString($id);

        if ($path->isRoot()) {
            throw new \Exception(\sprintf('Cannot remove root directory.'));
        }

        $cwd = $this->get($id);

        if (($cwd instanceof DirectoryInterface) && (null !== $parent = $cwd->getParent())) {
            $parent->remove($cwd);
            $parent = $cwd->getParent();

            if ($parent instanceof DirectoryInterface) {
                return $parent;
            }
        }

        return $this;
    }
}
