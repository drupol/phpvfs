<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phptree\Node\AttributeNodeInterface;

interface DirectoryInterface extends AttributeNodeInterface
{

    /**
     * @param string $id
     *
     * @return \drupol\phpvfs\Node\VfsInterface|\drupol\phpvfs\Node\DirectoryInterface|\drupol\phpvfs\Node\FileInterface|null
     */
    public function containsAttributeId(string $id): ?VfsInterface;

    /**
     * @return \drupol\phpvfs\Node\DirectoryInterface|null
     */
    public function root(): ?DirectoryInterface;
}
