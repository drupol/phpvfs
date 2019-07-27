<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phptree\Node\AttributeNode;
use drupol\phptree\Node\NodeInterface;
use drupol\phpvfs\Utils\Path;

/**
 * Class FilesystemNode.
 */
abstract class FilesystemNode extends AttributeNode implements FilesystemNodeInterface
{
    /**
     * FilesystemNode constructor.
     *
     * @param array $attributes
     * @param null|int $capacity
     */
    public function __construct(
        array $attributes = [],
        ?int $capacity = 0
    ) {
        $time = time();

        $attributes = [
            'uid' => \function_exists('posix_getuid') ? posix_getuid() : 0,
            'gid' => \function_exists('posix_getgid') ? posix_getgid() : 0,
            'atime' => $time,
            'mtime' => $time,
            'ctime' => $time,
        ] + $attributes;

        parent::__construct($attributes, $capacity);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     *
     * @return \drupol\phpvfs\Node\DirectoryInterface
     */
    public function add(NodeInterface ...$nodes): NodeInterface
    {
        foreach ($nodes as $node) {
            if (!($node instanceof FilesystemNodeInterface)) {
                throw new \Exception('Invalid filesystem node type.');
            }

            $node = $node->root();

            if ($this->getAttribute('id') === $node->getAttribute('id')) {
                $this->add($node[0]->setParent(null));

                continue;
            }

            // If the $cwd contains the nodechild.
            if (null !== $child = $this->contains($node)) {
                if (0 !== $node->degree()) {
                    $child->add($node[0]->setParent(null));
                }

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
            array_unshift($paths, $ancestor->getAttribute('id'));
        }

        return Path::fromString(str_replace('//', '/', implode('/', $paths)));
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
    private function contains(FilesystemNodeInterface $node): ?FilesystemNodeInterface
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
