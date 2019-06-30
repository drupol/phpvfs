<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phptree\Node\AttributeNodeInterface;
use drupol\phpvfs\Utils\Path;

/**
 * Interface FilesystemNodeInterface.
 */
interface FilesystemNodeInterface extends AttributeNodeInterface
{
    /**
     * @return \drupol\phpvfs\Utils\Path
     */
    public function getPath(): Path;

    /**
     * @return \drupol\phpvfs\Node\FilesystemNodeInterface
     */
    public function root(): FilesystemNodeInterface;
}
