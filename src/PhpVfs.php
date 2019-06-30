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

class PhpVfs
{
    public const SCHEME = 'phpvfs';

    /**
     * @var array
     */
    public $context;

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
                'currentFile' => null,
            ] + $options,
        ];

        \stream_context_set_default($options);
        \stream_wrapper_register(self::SCHEME, __CLASS__);
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function rename(string $from, string $to): bool
    {
        $from = $this->stripScheme($from);
        $to = $this->stripScheme($to);

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
     * @see http://php.net/streamwrapper.stream-close
     */
    public function stream_close(): void // phpcs:ignore
    {
        if (null !== $file = $this->getCurrentFile()) {
            $file->setPosition(0);
        }

        $this->setCurrentFile(null);
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
     * @param mixed $mode
     * @param mixed $options
     * @param mixed $openedPath
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function stream_open(string $resource, $mode, $options, &$openedPath) // phpcs:ignore
    {
        $mode = \str_split(\str_replace('b', '', $mode));

        $appendMode = \in_array('a', $mode, true);
        $readMode = \in_array('r', $mode, true);
        $writeMode = \in_array('w', $mode, true);
        $extended = \in_array('+', $mode, true);

        $resource = $this->stripScheme($resource);

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
     * @see http://php.net/streamwrapper.stream-read
     *
     * @param int $bytes
     *
     * @return mixed
     */
    public function stream_read(int $bytes) // phpcs:ignore
    {
        if (null !== $file = $this->getCurrentFile()) {
            return $file->read($bytes);
        }

        return false;
    }

    /**
     * @return array
     */
    public function stream_stat(): array // phpcs:ignore
    {
        if (null === $file = $this->getCurrentFile()) {
            return [];
        }

        return (array) $file->getAttributes();
    }

    /**
     * @param string $data
     *
     * @return int
     */
    public function stream_write(string $data) // phpcs:ignore
    {
        if (null !== $file = $this->getCurrentFile()) {
            return $file->write($data);
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
     * @todo
     */
    public static function unregister()
    {
        \stream_wrapper_unregister(self::SCHEME);
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
