<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Exporter;

use drupol\phptree\Exporter\Ascii;
use drupol\phptree\Node\NodeInterface;
use drupol\phpvfs\Node\VfsInterface;

class AttributeAscii extends Ascii
{
    /**
     * {@inheritdoc}
     */
    protected function getNodeRepresentation(NodeInterface $node): string
    {
        return $node->getPath()->__toString();
    }
    /**
     * {@inheritdoc}
     */
    protected function isValidNode(NodeInterface $node): bool
    {
        return $node instanceof VfsInterface;
    }
}
