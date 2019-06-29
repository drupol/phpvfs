<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phptree\Node\AttributeNode;
use drupol\phptree\Node\NodeInterface;
use drupol\phpvfs\Utils\Path;

/**
 * Class Vfs.
 */
abstract class FilesystemNode extends AttributeNode implements FilesystemNodeInterface
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
        /** @var \drupol\phpvfs\Node\FilesystemNodeInterface $node */
        foreach ($nodes as $node) {
            $node = $node->root();

            if (!($node instanceof FilesystemNodeInterface)) {
                throw new \Exception('Invalid filesystem node type.');
            }

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
    public function root(): FilesystemNodeInterface
    {
        $root = $this;

        foreach ($this->getAncestors() as $ancestor) {
            $root = $ancestor;
        }

        return $root;
    }

    /**
     * @param \drupol\phpvfs\Node\FilesystemNodeInterface $node
     *
     * @return null|\drupol\phpvfs\Node\FilesystemNodeInterface
     */
    protected function contains(FilesystemNodeInterface $node): ?FilesystemNodeInterface
    {
        /** @var \drupol\phpvfs\Node\FilesystemNodeInterface $child */
        foreach ($this->children() as $child) {
            if ($node->getAttribute('id') === $child->getAttribute('id')) {
                return $child;
            }
        }

        return null;
    }
}
