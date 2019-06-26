<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Importer;

use drupol\phptree\Node\Node;
use drupol\phptree\Node\NodeInterface;
use drupol\phpvfs\Node\Directory;
use drupol\phpvfs\Node\File;

/**
 * Class SimpleArray.
 */
class SimpleArray extends \drupol\phptree\Importer\SimpleArray
{
    /**
     * {@inheritdoc}
     */
    public function import($data): NodeInterface
    {
        $root = new Directory('root');

        $this->arrayToTree($data, $root);

        return $root;
    }

    /**
     * Convert an array into a tree.
     *
     * @param array $data
     * @param null|\drupol\phptree\Node\NodeInterface $parent
     *
     * @return \drupol\phptree\Node\NodeInterface
     *   The tree
     */
    protected function arrayToTree(array $data, NodeInterface $parent = null): NodeInterface
    {
        foreach ($data as $key => $value) {
            if (\is_array($value)) {
                $dir = new Directory($key);
                $parent->add($dir);
                $this->arrayToTree($value, $dir);
            }

            if (\is_string($value)) {
                $file = new File($value);
                $parent->add($file);
            }
        }

        return $parent;
    }

    /**
     * Create a node.
     *
     * @param mixed $data
     *   The arguments
     *
     * @return \drupol\phptree\Node\NodeInterface
     *   The node
     */
    protected function createNode($data): NodeInterface
    {
        return new Node();
    }
}
