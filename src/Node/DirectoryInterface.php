<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

/**
 * Interface DirectoryInterface.
 */
interface DirectoryInterface extends FilesystemNodeInterface
{
    /**
     * @param string $name
     *
     * @return \drupol\phpvfs\Node\DirectoryInterface
     */
    public function cd(string $name): DirectoryInterface;

    /**
     * @param string $id
     *
     * @return null|\drupol\phpvfs\Node\DirectoryInterface|\drupol\phpvfs\Node\FileInterface|\drupol\phpvfs\Node\FilesystemNodeInterface
     */
    public function containsAttributeId(string $id): ?FilesystemNodeInterface;

    /**
     * @param string ...$names
     *
     * @return bool
     */
    public function exist(string ...$names): bool;

    /**
     * @param string $name
     *
     * @return \drupol\phpvfs\Node\FilesystemNodeInterface
     */
    public function get(string $name): FilesystemNodeInterface;

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function mkdir(string $name);

    /**
     * @param string $name
     *
     * @return \drupol\phpvfs\Node\DirectoryInterface
     */
    public function rmdir(string $name): DirectoryInterface;
}
