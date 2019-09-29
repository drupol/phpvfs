<?php

declare(strict_types=1);

namespace drupol\phpvfs\Node;

use drupol\phpvfs\Utils\Path;

/**
 * Class File.
 */
class File extends FilesystemNode implements FileInterface
{
    /**
     * File constructor.
     *
     * @param array $attributes
     */
    public function __construct(
        array $attributes = []
    ) {
        $attributes += [
            'content' => '',
            'position' => 0,
        ];

        parent::__construct($attributes, null);
    }

    /**
     * @param string $id
     * @param string $content
     * @param array $attributes
     *
     * @throws \Exception
     *
     * @return \drupol\phpvfs\Node\File
     */
    public static function create(string $id, string $content = '', array $attributes = [])
    {
        $path = Path::fromString($id);

        $dirname = $path->dirname();

        $basedir = null;

        if ('' !== $dirname && '.' !== $dirname) {
            $basedir = Directory::create($dirname);
        }

        $attributes = [
            'id' => $path->basename(),
            'content' => $content,
        ] + $attributes;

        $file = new self($attributes);

        if (null !== $basedir) {
            $basedir->add($file);
        }

        return $file;
    }

    /**
     * @return string
     */
    public function read(): string
    {
        return $this->getAttribute('content');
    }

    /**
     * @param string $data
     *
     * @return \drupol\phpvfs\Node\FileInterface
     */
    public function write(string $data): FileInterface
    {
        $this->setAttribute('content', $data);

        return $this;
    }
}
