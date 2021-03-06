<?php

declare(strict_types=1);

namespace drupol\phpvfs\Filesystem;

use drupol\phpvfs\Node\DirectoryInterface;

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
     * @param \drupol\phpvfs\Node\DirectoryInterface $directory
     */
    public function __construct(DirectoryInterface $directory)
    {
        $this->cwd = $directory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCwd(): DirectoryInterface
    {
        return $this->cwd;
    }

    /**
     * @return string
     */
    public function pwd(): string
    {
        return (string) $this->getCwd()->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function root(): DirectoryInterface
    {
        $root = $this->getCwd()->root();

        if ($root instanceof DirectoryInterface) {
            return $root;
        }

        throw new \Exception('Unable to get root directory.');
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
