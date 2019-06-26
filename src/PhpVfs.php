<?php

declare(strict_types = 1);

namespace drupol\phpvfs;

use drupol\phpvfs\Node\Directory;

class PhpVfs
{
    public const ROOT = '/';
    public const SCHEME = 'phpvfs';

    /**
     * @var \drupol\phpvfs\Node\Directory
     */
    private $root;

    /**
     * PhpVfs constructor.
     */
    public function __construct()
    {
        $this->root = $this->mkdir('/', 0777, 0);
    }

    public function mkdir(string $path, int $mode, int $options)
    {
        $paths = \array_filter(\explode('/', $path));

        if ([] === $paths) {
            return true;
        }

        foreach ($paths as $pathsItem) {
            $this->root->add(Directory::create($pathsItem));
        }

        return true;
    }

    public static function register()
    {
        \stream_wrapper_register(self::SCHEME, __CLASS__);
    }

    public function stream_open($dir)
    {
        $t = $this->splitPath($dir);
        $r = $this->resolvePath($dir);

        \xdebug_break();
    }
    /**
     * helper method to resolve a path from /foo/bar/. to /foo/bar.
     *
     * @param   string  $path
     *
     * @return  string
     */
    protected function resolvePath(string $path): string
    {
        $newPath = [];

        foreach (\explode('/', $path) as $pathPart) {
            if ('.' === $pathPart || '..' === $pathPart) {
                continue;
            }

            $newPath[] = $pathPart;

            if (1 < \count($newPath)) {
                \array_pop($newPath);
            }
        }

        return \implode('/', $newPath);
    }

    /**
     * splits path into its dirname and the basename.
     *
     * @param   string  $path
     *
     * @return  string[]
     */
    protected function splitPath(string $path): array
    {
        $lastSlashPos = \strrpos($path, '/');
        if (false === $lastSlashPos) {
            return ['dirname' => '', 'basename' => $path];
        }

        return [
            'dirname' => \substr($path, 0, $lastSlashPos),
            'basename' => \substr($path, $lastSlashPos + 1),
        ];
    }
}
