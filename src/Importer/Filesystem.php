<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Importer;

use drupol\phptree\Importer\ImporterInterface;
use drupol\phptree\Node\NodeInterface;
use drupol\phpvfs\Node\Directory;
use drupol\phpvfs\Node\File;
use drupol\phpvfs\Node\FilesystemNodeInterface;
use drupol\phpvfs\Utils\Path;

/**
 * Class Filesystem.
 */
class Filesystem implements ImporterInterface
{
    /**
     * Import data into a node.
     *
     * @param mixed $data
     *   The data to import
     *
     * @throws \Exception
     *
     * @return \drupol\phptree\Node\NodeInterface
     *   The new node
     */
    public function import($data): NodeInterface
    {
        if (!\is_string($data)) {
            throw new \Exception('Must be a string.');
        }

        if (!\file_exists($data)) {
            throw new \Exception('The directory doesn\'t exist.');
        }

        if (($root = $this->arrayToTree($this->doImport($data))) instanceof FilesystemNodeInterface) {
            $root->setAttribute('label', $data);
        }

        return $root;
    }

    /**
     * Create a node.
     *
     * @param mixed $data
     *   The arguments
     *
     * @return \drupol\phpvfs\Node\FilesystemNodeInterface
     *   The node
     */
    protected function createNode($data): FilesystemNodeInterface
    {
        $node = new Directory();
        $label = Path::fromString($data)->getLastPart();
        $shape = 'square';

        if (\is_file($data)) {
            $node = new File();
            $label = Path::fromString($data)->basename();
            $shape = 'circle';
        }

        $node->setAttribute('label', $label);
        $node->setAttribute('shape', $shape);
        $node->setAttribute('id', $data);

        return $node;
    }

    /**
     * Convert an array into a tree.
     *
     * @param array $data
     *
     * @return \drupol\phptree\Node\NodeInterface
     *   The tree
     */
    private function arrayToTree(array $data): NodeInterface
    {
        $data += [
            'children' => [],
        ];

        $node = $this->createNode($data['name']);

        $children = \array_map(
            [$this, 'arrayToTree'],
            $data['children']
        );

        return $node->add(...$children);
    }

    /**
     * @param string $directory
     *
     * @return array
     */
    private function doImport(string $directory): array
    {
        $children = [];

        if (false !== $items = \glob($directory . '/*')) {
            $children = \array_map(
                [$this, 'doImport'],
                $items
            );
        }

        return [
            'name' => $directory,
            'children' => $children,
        ];
    }
}
