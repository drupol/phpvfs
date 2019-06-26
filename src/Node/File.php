<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phptree\Node\AttributeNode;
use drupol\phptree\Node\AttributeNodeInterface;

class File extends AttributeNode
{
    /**
     * @param string $id
     * @param array $attributes
     *
     * @throws \Exception
     *
     * @return \drupol\phpvfs\Node\File
     */
    public static function create(string $id, string $content = null, array $attributes = [])
    {
        $absolute = false;
        if (0 === \strpos($id, \DIRECTORY_SEPARATOR, 0)) {
            $absolute = true;
        }

        if (\DIRECTORY_SEPARATOR !== $id && false !== \strpos($id, \DIRECTORY_SEPARATOR)) {
            $paths = \array_filter(\explode(\DIRECTORY_SEPARATOR, $id));

            if (true === $absolute) {
                $paths = \array_merge([\DIRECTORY_SEPARATOR], $paths);
            }

            $return = $root = self::create(\array_shift($paths));

            foreach ($paths as $path) {
                if (\end($paths) === $path) {
                    $child = self::create($path, $content, $attributes);
                } else {
                    $child = Directory::create($path);
                }
                $root->add($child);
                $root = $child;
            }

            return $return;
        }

        $attributes = [
            'id' => $id,
            'content' => $content,
        ] + $attributes;

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

            if (false === $this->contains($pathItem)) {
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

    public function getPath()
    {
        $paths = [
            $this->getAttribute('id'),
        ];

        foreach ($this->getAncestors() as $ancestor) {
            \array_unshift($paths, $ancestor->getAttribute('id'));
        }

        return \str_replace('//', '/', \implode('/', $paths));
    }

    public function mkdir(string $id)
    {
        $cwd = $this;

        if (0 === \strpos($id, \DIRECTORY_SEPARATOR, 0)) {
            $cwd = $this->root();
        }

        if (\DIRECTORY_SEPARATOR !== $id && false !== \strpos($id, \DIRECTORY_SEPARATOR)) {
            $paths = \array_values(\array_filter(\explode(\DIRECTORY_SEPARATOR, $id)));

            if ([] !== $paths && false !== $child = $cwd->contains(Directory::create($paths[0]))) {
                \array_shift($paths);

                return $child->mkdir(\implode(\DIRECTORY_SEPARATOR, $paths));
            }

            foreach ($paths as $path) {
                $child = new self(['id' => $path]);
                $cwd->add($child);
                $cwd = $child;
            }

            return $this;
        }

        $this->add(Directory::create($id));

        return $this;
    }

    public function root(): AttributeNodeInterface
    {
        $root = $this;

        foreach ($this->getAncestors() as $ancestor) {
            $root = $ancestor;
        }

        return $root;
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
