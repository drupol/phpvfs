<?php

declare(strict_types = 1);

namespace drupol\phpvfs;

use drupol\phpvfs\Commands\Exist;
use drupol\phpvfs\Commands\Get;
use drupol\phpvfs\Filesystem\Filesystem;
use drupol\phpvfs\Filesystem\FilesystemInterface;
use drupol\phpvfs\Node\File;
use drupol\phpvfs\Node\FileInterface;

class PhpVfs
{
    public const SCHEME = 'phpvfs';

    /**
     * @var array
     */
    public $context;

    /**
     * @var null|\drupol\phpvfs\Node\FileInterface
     */
    private $currentFile;

    /**
     * @return \drupol\phpvfs\Filesystem\FilesystemInterface
     */
    public static function fs(): FilesystemInterface
    {
        $options = \stream_context_get_options(
            \stream_context_get_default()
        );

        return $options[static::SCHEME]['filesystem'];
    }

    /**
     * @param \drupol\phpvfs\Filesystem\Filesystem $filesystem
     * @param array $options
     */
    public static function register(Filesystem $filesystem, array $options = [])
    {
        $options = [
            static::SCHEME => [
                'filesystem' => $filesystem,
            ] + $options,
        ];

        \stream_context_set_default($options);
        \stream_wrapper_register(self::SCHEME, __CLASS__);
    }

    /**
     * @see http://php.net/streamwrapper.stream-close
     */
    public function stream_close() // phpcs:ignore
    {
        if (null !== $this->currentFile) {
            $this->currentFile->setPosition(0);
        }

        $this->currentFile = null;
    }

    /**
     * @return bool
     *
     * @see http://php.net/streamwrapper.stream-eof
     */
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

        if (true === $this::fs()->exist($resource)) {
            $file = $this::fs()->get($resource);

            if ($file instanceof FileInterface) {
                $this->currentFile = $file;
            }
        } else {
            $this->currentFile = File::create($resource);
            $this::fs()->getCwd()->add($this->currentFile->root());
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
     * @param string $path
     *
     * @throws \Exception
     */
    public function unlink(string $path)
    {
        $path = $this->stripScheme($path);

        if (true === Exist::exec($this::fs(), $path)) {
            $file = Get::exec($this::fs(), $path);

            if (null !== $parent = $file->getParent()) {
                $parent->delete($file);
            }
        }
    }

    /**
     * Returns path stripped of url scheme (http://, ftp://, test:// etc.).
     *
     * @param string $path
     *
     * @return string
     */
    protected function stripScheme(string $path): string
    {
        return '/' . \ltrim(\substr($path, 9), '/');
    }
}
