<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phptree\Node\AttributeNodeInterface;

interface DirectoryInterface extends AttributeNodeInterface
{
    /**
     * @param string $id
     *
     * @return null|\drupol\phpvfs\Node\DirectoryInterface|\drupol\phpvfs\Node\FileInterface|\drupol\phpvfs\Node\VfsInterface
     */
    public function containsAttributeId(string $id): ?VfsInterface;

    /**
     * @return null|\drupol\phpvfs\Node\DirectoryInterface
     */
    public function root(): ?DirectoryInterface;
}
