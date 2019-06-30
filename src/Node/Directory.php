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
}
