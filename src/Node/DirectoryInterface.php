<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

interface DirectoryInterface extends VfsInterface
{
    /**
     * @param string $id
     *
     * @return null|\drupol\phpvfs\Node\DirectoryInterface|\drupol\phpvfs\Node\FileInterface|\drupol\phpvfs\Node\VfsInterface
     */
    public function containsAttributeId(string $id): ?VfsInterface;
}
