<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Filesystem;

use drupol\phpvfs\Node\DirectoryInterface;
use drupol\phpvfs\Node\VfsInterface;

interface FilesystemInterface
{
    public function exist(string $id): bool;

    public function get(string $id): ?VfsInterface;
    public function getCwd(): DirectoryInterface;

    public function inspect(string $id): string;

    public function setCwd(DirectoryInterface $directory);
}
