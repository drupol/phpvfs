<?php

declare(strict_types = 1);

namespace drupol\phpvfs;

use drupol\phpvfs\Filesystem\FilesystemInterface;
use drupol\phpvfs\Node\File;
use drupol\phpvfs\Node\FileInterface;

class PhpVfs
{
    public const SCHEME = 'phpvfs';

    public $context;

    /**
     * @var null|\drupol\phpvfs\Node\FileInterface
     */
    private $currentFile;

    public function getVfs(): FilesystemInterface
    {
        $options = \stream_context_get_options(\stream_context_get_default());

        return $options[static::SCHEME]['vfs'];
    }

    /**
     * @param string $path
     * @param int $mode
     * @param int $options
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function mkdir(string $path, int $mode, int $options)
    {
        $this->getVfs()->getCwd()->mkdir($path);

        return true;
    }

    public static function register()
    {
        \stream_wrapper_register(self::SCHEME, __CLASS__);
    }

    /**
     * @see http://php.net/streamwrapper.stream-close
     */
    public function stream_close() // phpcs:ignore
    {
        $this->currentFile = null;
    }

    public function stream_eof(): bool // phpcs:ignore
    {
        return true;
    }

    /**
     * @param string $resource
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function stream_open(string $resource) // phpcs:ignore
    {
        $resource = $this->stripScheme($resource);

        if (true === $this->getVfs()->exist($resource)) {
            $file = $this->getVfs()->get($resource);

            if ($file instanceof FileInterface) {
                $this->currentFile = $file;
            }
        } else {
            $this->currentFile = File::create($resource);
            $this->getVfs()->getCwd()->add($this->currentFile->root());
        }

        if (null === $this->currentFile) {
            return false;
        }

        $this->currentFile->setPosition(0);

        return true;
    }

    /**
     * @see http://php.net/streamwrapper.stream-read
     *
     * @param int $bytes
     *
     * @return mixed
     */
    public function stream_read(int $bytes) // phpcs:ignore
    {
        if (null !== $this->currentFile) {
            return $this->currentFile->read($bytes);
        }

        return false;
    }

    /**
     * @param string $data
     *
     * @return int
     */
    public function stream_write(string $data) // phpcs:ignore
    {
        if (null !== $this->currentFile) {
            return $this->currentFile->write($data);
        }

        return 0;
    }

    /**
     * Returns path stripped of url scheme (http://, ftp://, test:// etc.).
     *
     * @param string $path
     *
     * @return string
     */
    public function stripScheme($path): string
    {
        return '/' . \ltrim(\substr($path, 0, 8), '/');
    }
}
