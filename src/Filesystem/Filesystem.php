<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Filesystem;

use drupol\phpvfs\Commands\Cd;
use drupol\phpvfs\Commands\Exist;
use drupol\phpvfs\Commands\Inspect;
use drupol\phpvfs\Commands\Touch;
use drupol\phpvfs\Node\Directory;
use drupol\phpvfs\Node\DirectoryInterface;
use drupol\phpvfs\Node\File;

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
    public static function directory(string $id, array $attributes = []): DirectoryInterface
    {
        return Directory::create($id, $attributes);
    }

    public function exist(string $id): bool
    {
        return Exist::exec($this, $id);
    }

    /**
     * {@inheritdoc}
     */
    public static function file(string $id, string $content = null, array $attributes = []): File
    {
        return File::create($id, $content, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getCwd(): DirectoryInterface
    {
        return $this->cwd;
    }

    public function inspect(string $id): string
    {
        return Inspect::exec($this, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function root(): DirectoryInterface
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
