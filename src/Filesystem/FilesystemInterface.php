<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Filesystem;

use drupol\phpvfs\Node\DirectoryInterface;

interface FilesystemInterface
{
    public function getCwd(): DirectoryInterface;

    public function setCwd(DirectoryInterface $directory);
}
