<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phptree\Node\AttributeNode;
use drupol\phptree\Node\NodeInterface;
use drupol\phpvfs\Utils\Path;

/**
 * Class Vfs.
 */
abstract class Vfs extends AttributeNode implements VfsInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     *
     * @return \drupol\phpvfs\Node\DirectoryInterface
     */
    public function add(NodeInterface ...$nodes): NodeInterface
    {
        /** @var \drupol\phpvfs\Node\VfsInterface $node */
        foreach ($nodes as $node) {
            if ($this->getAttribute('id') === $node->getAttribute('id')) {
                $this->add($node[0]->setParent(null));

                continue;
            }

            // If the $cwd contains the nodechild.
            if (null !== $child = $this->contains($node)) {
                $child->add($node[0]->setParent(null));

                continue;
            }

            parent::add($node->setParent(null));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): Path
    {
        $paths = [
            $this->getAttribute('id'),
        ];

        foreach ($this->getAncestors() as $ancestor) {
            \array_unshift($paths, $ancestor->getAttribute('id'));
        }

        return Path::fromString(\str_replace('//', '/', \implode('/', $paths)));
    }

    /**
     * {@inheritdoc}
     */
    public function root(): VfsInterface
    {
        $root = $this;

        foreach ($this->getAncestors() as $ancestor) {
            $root = $ancestor;
        }

        return $root;
    }

    /**
     * @param \drupol\phpvfs\Node\VfsInterface $node
     *
     * @return null|\drupol\phpvfs\Node\VfsInterface
     */
    protected function contains(VfsInterface $node): ?VfsInterface
    {
        /** @var \drupol\phpvfs\Node\VfsInterface $child */
        foreach ($this->children() as $child) {
            if ($node->getAttribute('id') === $child->getAttribute('id')) {
                return $child;
            }
        }

        return null;
    }
}
