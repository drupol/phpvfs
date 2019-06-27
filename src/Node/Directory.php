<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phptree\Node\AttributeNodeInterface;
use drupol\phpvfs\Utils\Path;

class Directory extends Vfs implements DirectoryInterface
{
    /**
     * @param string $id
     *
     * @return null|\drupol\phpvfs\Node\DirectoryInterface|\drupol\phpvfs\Node\FileInterface|\drupol\phpvfs\Node\VfsInterface
     */
    public function containsAttributeId(string $id): ?VfsInterface
    {
        /** @var \drupol\phptree\Node\AttributeNodeInterface $child */
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

            $return = $root = self::create($firstPart, $attributes);

            foreach ($path->getIterator() as $pathPart) {
                $child = new self(['id' => $pathPart]);
                $root->add($child);
                $root = $child;
            }

            return $return;
        }

        $attributes = ['id' => $id] + $attributes;

        return new self($attributes);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function exists(string $path)
    {
        $paths = \array_filter(\explode('/', $path));
        $pathItem = \reset($paths);
        $found = false;

        /** @var \drupol\phpvfs\Node\Directory $child */
        foreach ($this->children() as $child) {
            if (!($child instanceof Directory)) {
                continue;
            }

            if (null === $this->containsAttributeId($pathItem)) {
                $found = false;

                break;
            }

            if (1 === \count($paths)) {
                return true;
            }

            \array_shift($paths);

            return $found || $child->exists(\implode('/', $paths));
        }

        return $found;
    }

    public function mkdir(string $id)
    {
        return $this->add(Directory::create($id));
    }

    /**
     * @param \drupol\phptree\Node\AttributeNodeInterface $node
     *
     * @return AttributeNodeInterface|bool
     */
    protected function contains(AttributeNodeInterface $node)
    {
        /** @var \drupol\phptree\Node\AttributeNodeInterface $child */
        foreach ($this->children() as $child) {
            if ($node->getAttribute('id') === $child->getAttribute('id')) {
                return $child;
            }
        }

        return false;
    }
}
