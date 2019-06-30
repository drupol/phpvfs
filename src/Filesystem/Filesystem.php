<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Filesystem;

use drupol\phpvfs\Node\Directory;
use drupol\phpvfs\Node\DirectoryInterface;
use drupol\phpvfs\Node\FilesystemNodeInterface;

/**
 * Class Filesystem.
 */
class Filesystem implements FilesystemInterface
{
    /**
     * @var \drupol\phpvfs\Node\DirectoryInterface
     */
    private $cwd;

    /**
     * Filesystem constructor.
     *
     * @param string $id
     * @param array $attributes
     *
     * @throws \Exception
     */
    public function __construct(
        string $id,
        array $attributes = []
    ) {
        $attributes = [
            'id' => $id,
            'vfs' => $this,
        ] + $attributes;

        $this->cwd = Directory::create($id, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public static function create(string $id, array $attributes = [])
    {
        return new self($id, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getCwd(): DirectoryInterface
    {
        return $this->cwd;
    }

    /**
     * {@inheritdoc}
     */
    public function root(): FilesystemNodeInterface
    {
        return $this->cwd->root();
    }

    /**
     * {@inheritdoc}
     */
    public function setCwd(DirectoryInterface $dir)
    {
        $this->cwd = $dir;

        return $this;
    }
}
