<?php

declare(strict_types = 1);

namespace drupol\phpvfs;

use drupol\phpvfs\Command\Cd;
use drupol\phpvfs\Command\Exist;
use drupol\phpvfs\Command\Get;
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
    public static function register(Filesystem $filesystem, array $options = [])
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
        if (!Exist::exec($this::fs(), $from)) {
            throw new \Exception('Source resource does not exist.');
        }

        $from = Get::exec($this::fs(), $from);

        if (Exist::exec($this::fs(), $to)) {
            throw new \Exception('Destination already exist.');
        }

        $toPath = Path::fromString($to);

        $this::fs()
            ->getCwd()
            ->mkdir($toPath->dirname());

        if (null !== $parent = $from->getParent()) {
            $parent->delete($from);
        }

        Cd::exec($this::fs(), $toPath->dirname());

        $from->setAttribute('id', $toPath->basename());

        if ($from instanceof FileInterface) {
            $from->setPosition(0);
        }

        $this::fs()
            ->getCwd()
            ->add($from);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rmdir(string $path, int $options): bool
    {
        throw new \Exception('Not implemented yet.');
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
        if (null !== $file = $this->getCurrentFile()) {
            $file->setPosition(0);
        }

        $this->setCurrentFile(null);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_eof(): bool // phpcs:ignore
    {
        return true;
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
        $mode = \str_split(\str_replace('b', '', $mode));

        $appendMode = \in_array('a', $mode, true);
        $readMode = \in_array('r', $mode, true);
        $writeMode = \in_array('w', $mode, true);
        $extended = \in_array('+', $mode, true);

        $resourcePath = Path::fromString($resource);

        if (!Exist::exec($this::fs(), $resource)) {
            if ($readMode || !Exist::exec($this::fs(), $resourcePath->dirname())) {
                if ($options & STREAM_REPORT_ERRORS) {
                    \trigger_error(\sprintf('%s: failed to open stream.', $resourcePath), E_USER_WARNING);
                }

                return false;
            }

            $file = File::create($resource);

            $this->setCurrentFile($file);
            $this::fs()
                ->getCwd()
                ->add($file->root());
        }

        if (null === $file = $this::fs()->get($resource)) {
            return false;
        }

        if (!($file instanceof FileInterface)) {
            return false;
        }

        $file->setPosition(0);

        $this->setCurrentFile($file);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function stream_read(int $bytes) // phpcs:ignore
    {
        if (null !== $file = $this->getCurrentFile()) {
            return $file->read($bytes);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool // phpcs:ignore
    {
        throw new \Exception('Not implemented yet.');
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

        return (array) $file->getAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function stream_tell(): int // phpcs:ignore
    {
        throw new \Exception('Not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function stream_write(string $data): int // phpcs:ignore
    {
        if (null !== $file = $this->getCurrentFile()) {
            return $file->write($data);
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function unlink(string $path): bool
    {
        if (true === Exist::exec($this::fs(), $path)) {
            $file = Get::exec($this::fs(), $path);

            if (null !== $parent = $file->getParent()) {
                $parent->delete($file);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function unregister()
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
     * @return null|FileInterface
     */
    protected function getCurrentFile(): ?FileInterface
    {
        $options = \stream_context_get_options(
            \stream_context_get_default()
        );

        return $options[static::SCHEME]['currentFile'];
    }

    /**
     * @param null|FileInterface $file
     */
    protected function setCurrentFile(?FileInterface $file)
    {
        $options = \stream_context_get_options(
            \stream_context_get_default()
        );

        $options[static::SCHEME]['currentFile'] = $file;

        \stream_context_set_default($options);
    }
}
