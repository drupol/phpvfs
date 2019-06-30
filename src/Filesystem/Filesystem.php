<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Filesystem;

use drupol\phpvfs\Command\Cd;
use drupol\phpvfs\Command\Exist;
use drupol\phpvfs\Command\Get;
use drupol\phpvfs\Command\Inspect;
use drupol\phpvfs\Command\Touch;
use drupol\phpvfs\Node\Directory;
use drupol\phpvfs\Node\DirectoryInterface;
use drupol\phpvfs\Node\FilesystemNodeInterface;

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
    public function cd(string $id)
    {
        Cd::exec($this, $id);

        return $this;
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
    public function exist(string $id): bool
    {
        return Exist::exec($this, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $id): ?FilesystemNodeInterface
    {
        return Get::exec($this, $id);
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
    public function inspect(string $id): string
    {
        return Inspect::exec($this, $id);
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

    /**
     * {@inheritdoc}
     */
    public function touch(string $id)
    {
        Touch::exec($this, $id);

        return $this;
    }
}
