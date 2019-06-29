<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Exporter;

use drupol\phptree\Exporter\Ascii;
use drupol\phptree\Node\NodeInterface;
use drupol\phpvfs\Node\FilesystemNodeInterface;

class AttributeAscii extends Ascii
{
    /**
     * @param FilesystemNodeInterface $node
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
