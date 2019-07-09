<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Exporter;

use drupol\phptree\Exporter\Ascii;
use drupol\phptree\Node\NodeInterface;
use drupol\phpvfs\Node\FilesystemNodeInterface;

/**
 * Class AttributeAscii.
 */
final class AttributeAscii extends Ascii
{
    /**
     * @param \drupol\phptree\Node\NodeInterface $node
     *   The node.
     *
     * @return string
     *   The node representation.
     */
    protected function getNodeRepresentation(NodeInterface $node): string
    {
        if ($node instanceof FilesystemNodeInterface) {
            return $node->getAttribute('label');
        }
    }
}
