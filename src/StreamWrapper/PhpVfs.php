<?php

declare(strict_types = 1);

namespace drupol\phpvfs\StreamWrapper;

use drupol\phpvfs\Filesystem\Filesystem;
use drupol\phpvfs\Filesystem\FilesystemInterface;
use drupol\phpvfs\Node\File;
use drupol\phpvfs\Node\FileInterface;
use drupol\phpvfs\Utils\Path;

/**
 * Class PhpVfs.
 */
class PhpVfs implements StreamWrapperInterface
{
    /**
     * The scheme.
     */
    protected const SCHEME = 'phpvfs';

    /**
     * The stream context.
     *
     * @var array
     */
    public $context;

    /**
     * {@inheritdoc}
     */
    public function dir_closedir(): bool // phpcs:ignore
    {
        throw new \Exception('Not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function dir_opendir(string $path, int $options): bool // phpcs:ignore
    {
        throw new \Exception('Not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function dir_readdir(): string // phpcs:ignore
    {
        throw new \Exception('Not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function dir_rewinddir(): bool // phpcs:ignore
    {
        throw new \Exception('Not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public static function fs(): FilesystemInterface
    {
        $options = \stream_context_get_options(
            \stream_context_get_default()
        );

        return $options[static::SCHEME]['filesystem'];
    }

    /**
     * {@inheritdoc}
     */
    public function mkdir(string $path, int $mode, int $options): bool
    {
        throw new \Exception('Not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public static function register(Filesystem $filesystem, array $options = []): void
    {
        $options = [
            static::SCHEME => [
                'filesystem' => $filesystem,
                'currentFile' => null,
            ] + $options,
        ];

        \stream_context_set_default($options);
        \stream_wrapper_register(self::SCHEME, __CLASS__);
    }

    /**
     * {@inheritdoc}
     */
    public function rename(string $from, string $to): bool
    {
        if (!$this::fs()->getCwd()->exist($from)) {
            throw new \Exception('Source resource does not exist.');
        }

        $from = $this::fs()->getCwd()->get($from);

        if ($this::fs()->getCwd()->exist($to)) {
            throw new \Exception('Destination already exist.');
        }

        $toPath = Path::fromString($to);

        $this::fs()
            ->getCwd()
            ->mkdir($toPath->dirname());

        if (null !== $parent = $from->getParent()) {
            $parent->delete($from);
        }

        $directory = $this::fs()->getCwd()->cd($toPath->dirname());

        $from->setAttribute('id', $toPath->basename());

        $directory
            ->add($from);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rmdir(string $path, int $options): bool
    {
        $cwd = $this::fs()
            ->getCwd()
            ->rmdir($path);

        $this::fs()
            ->setCwd($cwd);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function stream_cast(int $cast_as) // phpcs:ignore
    {
        throw new \Exception('Not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function stream_close(): void // phpcs:ignore
    {
        $this->setCurrentFile(null);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_eof(): bool // phpcs:ignore
    {
        if (!(($file = $this->getCurrentFile()) instanceof Handler\FileInterface)) {
            throw new \Exception('The current file does not implement FileInterface.');
        }

        return $file->getPosition() === $file->size();
    }

    /**
     * {@inheritdoc}
     */
    public function stream_flush(): bool // phpcs:ignore
    {
        \clearstatcache();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function stream_lock($operation): bool // phpcs:ignore
    {
        throw new \Exception('Not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function stream_open(string $resource, string $mode, int $options, ?string &$openedPath): bool // phpcs:ignore
    {
        $modeSplit = \str_split(\str_replace('b', '', $mode));

        $appendMode = \in_array('a', $modeSplit, true);
        $readMode = \in_array('r', $modeSplit, true);
        $writeMode = \in_array('w', $modeSplit, true);
        $extended = \in_array('+', $modeSplit, true);

        $resourcePath = Path::fromString($resource)
            ->withScheme(null);

        $resourceExist = $this::fs()->getCwd()->exist($resource);
        $resourceDirnameExist = $this::fs()->getCwd()->exist($resourcePath->dirname());

        if (false === $resourceExist) {
            if (true === $readMode) {
                if (0 !== ($options & STREAM_REPORT_ERRORS)) {
                    \trigger_error(
                        \sprintf(
                            '%s: failed to open stream: Unknown resource.',
                            $resourcePath
                        ),
                        E_USER_WARNING
                    );
                }

                return false;
            }

            $this::fs()
                ->getCwd()
                ->add(File::create($resource)->root());
        }

        $file = $this::fs()->getCwd()->get($resource);

        if (!($file instanceof FileInterface)) {
            if (0 !== ($options & STREAM_REPORT_ERRORS)) {
                \trigger_error(\sprintf('fopen(%s): failed to open stream: Not a file.', $resource), E_USER_WARNING);
            }

            return false;
        }

        $fileHandler = new Handler\File($file, $mode);

        if (true === $appendMode) {
            $fileHandler->seekToEnd();
        } elseif (true === $writeMode) {
            $fileHandler->truncate();
            \clearstatcache();
        }

        $this->setCurrentFile($fileHandler);

        $openedPath = (string) $file->getPath();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function stream_read(int $bytes) // phpcs:ignore
    {
        if ((null === $file = $this->getCurrentFile()) || (false === $file->isReadable())) {
            return false;
        }

        return $file->read($bytes);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool // phpcs:ignore
    {
        if (($file = $this->getCurrentFile()) instanceof Handler\File) {
            $file->setPosition($offset);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function stream_set_option(int $option, int $arg1, int $arg2): bool // phpcs:ignore
    {
        throw new \Exception('Not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function stream_stat(): array // phpcs:ignore
    {
        if (null === $file = $this->getCurrentFile()) {
            return [];
        }

        return (array) $file->getFile()->getAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function stream_tell(): int // phpcs:ignore
    {
        if (($file = $this->getCurrentFile()) instanceof Handler\File) {
            return $file->getPosition();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stream_truncate(int $bytes): bool // phpcs:ignore
    {
        if (($file = $this->getCurrentFile()) instanceof Handler\File) {
            $file->truncate($bytes);
            \clearstatcache();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function stream_write(string $data): int // phpcs:ignore
    {
        if ((null === $file = $this->getCurrentFile()) || (false === $file->isWritable())) {
            return 0;
        }

        return $file->write($data);
    }

    /**
     * {@inheritdoc}
     */
    public function unlink(string $path): bool
    {
        if (true === $this::fs()->getCwd()->exist($path)) {
            $file = $this::fs()->getCwd()->get($path);

            if (null !== $parent = $file->getParent()) {
                $parent->delete($file);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function unregister(): void
    {
        \stream_wrapper_unregister(self::SCHEME);
    }

    /**
     * {@inheritdoc}
     */
    public function url_stat(string $path, int $flags): array // phpcs:ignore
    {
        throw new \Exception('Not implemented yet.');
    }

    /**
     * @return null|\drupol\phpvfs\StreamWrapper\Handler\FileInterface
     */
    private function getCurrentFile(): ?Handler\FileInterface
    {
        $options = \stream_context_get_options(
            \stream_context_get_default()
        );

        return $options[static::SCHEME]['currentFile'];
    }

    /**
     * @param null|\drupol\phpvfs\StreamWrapper\Handler\FileInterface $file
     */
    private function setCurrentFile(?Handler\FileInterface $file): void
    {
        $options = \stream_context_get_options(
            \stream_context_get_default()
        );

        $options[static::SCHEME]['currentFile'] = $file;

        \stream_context_set_default($options);
    }
}
