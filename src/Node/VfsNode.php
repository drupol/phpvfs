<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phptree\Node\AttributeNode;
use drupol\phptree\Node\NodeInterface;

/**
 * Class VfsNode.
 */
abstract class VfsNode extends AttributeNode
{
    /**
     * {@inheritdoc}
     */
    public function add(NodeInterface ...$nodes): NodeInterface
    {
        foreach ($nodes as $node) {
            if ($this->getAttribute('id') === $node->getAttribute('id')) {
                $this->add($node[0]->setParent(null));
                continue;
            }

            // If the $cwd contains the nodechild.
            if (false !== $child = $this->contains($node)) {
                $child->add($node[0]->setParent(null));
                continue;
            }

            parent::add($node->setParent(null));
        }

        return $this;
    }
}
