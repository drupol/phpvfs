<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phptree\Node\AttributeNodeInterface;
use drupol\phpvfs\Utils\Path;

/**
 * Interface VfsInterface.
 */
interface VfsInterface extends AttributeNodeInterface
{
    /**
     * @return \drupol\phpvfs\Utils\Path
     */
    public function getPath(): Path;

    /**
     * @return \drupol\phpvfs\Node\VfsInterface
     */
    public function root(): VfsInterface;
}
