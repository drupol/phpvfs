<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Exporter;

use drupol\phptree\Exporter\Ascii;
use drupol\phptree\Node\NodeInterface;

/**
 * Class AttributeAscii.
 */
class AttributeAscii extends Ascii
{
    /**
     * @param \drupol\phpvfs\Node\FilesystemNodeInterface $node
     *   The node.
     *
     * @return string
     *   The node representation.
     */
    protected function getNodeRepresentation(NodeInterface $node): string
    {
        return $node->getPath()->__toString();
    }
}
