<?php

declare(strict_types = 1);

namespace drupol\phpvfs;

use drupol\phpvfs\Filesystem\FilesystemInterface;
use drupol\phpvfs\Node\File;

class PhpVfs
{
    public const SCHEME = 'phpvfs';

    public $context;

    /**
     * @var \drupol\phpvfs\Node\FileInterface
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
    public function stream_close()
    {
        $this->currentFile = null;
    }

    public function stream_eof(): bool
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
    public function stream_open(string $resource)
    {
        $resource = $this->stripScheme($resource);

        if (true === $this->getVfs()->exist($resource)) {
            $this->currentFile = $this->getVfs()->get($resource);
        } else {
            $this->currentFile = File::create($resource);
            $this->getVfs()->getCwd()->add($this->currentFile->root());
        }

        $this->currentFile->position(0);

        return true;
    }

    /**
     * @see http://php.net/streamwrapper.stream-read
     *
     * @return mixed
     */
    public function stream_read(int $bytes)
    {
        return $this->currentFile->read($bytes);
    }

    /**
     * @param string $data
     *
     * @return int
     */
    public function stream_write(string $data)
    {
        return $this->currentFile->write($data);
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
        $scheme = \explode('://', $path, 2);

        return '/' . \ltrim(\end($scheme), '/');
    }
}
