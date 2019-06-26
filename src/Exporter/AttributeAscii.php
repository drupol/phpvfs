<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Exporter;

use drupol\phptree\Exporter\Ascii;
use drupol\phptree\Node\AttributeNodeInterface;
use drupol\phptree\Node\NodeInterface;
use drupol\phptree\Node\ValueNodeInterface;

class AttributeAscii extends Ascii
{
    /**
     * Export the tree in an array.
     *
     * @param \drupol\phptree\Node\NodeInterface $node
     *   The node
     *
     * @return array
     *   The tree exported into an array
     */
    protected function doExportAsArray(NodeInterface $node): array
    {
        if (!($node instanceof AttributeNodeInterface)) {
            throw new \InvalidArgumentException('Must implements AttributeNodeInterface');
        }

        $children = [];
        /** @var ValueNodeInterface $child */
        foreach ($node->children() as $child) {
            $children[] = $this->doExportAsArray($child);
        }

        return [] === $children ?
        [$node->getPath()] :
        [$node->getPath(), $children];
    }
}
