<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Filesystem;

use drupol\phpvfs\Commands\Cd;
use drupol\phpvfs\Commands\Touch;
use drupol\phpvfs\Node\Directory;
use drupol\phpvfs\Node\File;

class Vfs
{
    public $cwd;

    public function __construct(
        string $id,
        array $attributes = []
    ) {
        $attributes = ['id' => $id] + $attributes;

        $this->cwd = Directory::create($id, $attributes);
    }

    public function cd(string $id)
    {
        Cd::exec($this, $id);

        return $this;
    }

    public static function create(string $id, array $attributes = [])
    {
        return new self($id, $attributes);
    }

    public static function directory(string $id, array $attributes = []): Directory
    {
        return Directory::create($id, $attributes);
    }

    public static function file(string $id, string $content = null, array $attributes = []): File
    {
        $attributes = ['id' => $id, 'content' => $content] + $attributes;

        return new File($attributes);
    }

    public function getCwd(): Directory
    {
        return $this->cwd;
    }

    public function root()
    {
        return $this->cwd->root();
    }

    public function setCwd(Directory $dir)
    {
        $this->cwd = $dir;

        return $this;
    }

    public function touch(string $id)
    {
        Touch::exec($this, $id);

        return $this;
    }
}
