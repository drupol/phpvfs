<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phpvfs\Utils\Path;

class File extends Vfs implements FileInterface
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
     * @return bool
     */
    public function atEof()
    {
        return $this->getPosition() >= \strlen($this->getAttribute('content'));
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
        if (!empty($dirname)) {
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
     * @return int
     */
    public function getPosition(): int
    {
        return (int) $this->getAttribute('position');
    }

    /**
     * @param int $bytes
     *
     * @return string
     */
    public function read(int $bytes): string
    {
        $data = \substr(
            $this->getAttribute('content'),
            $this->getPosition(),
            $bytes
        );

        $this->offsetPosition($bytes);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function seekToEnd()
    {
        $this->setPosition(
            \strlen($this->getAttribute('content'))
        );
    }

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setPosition(int $position): FileInterface
    {
        $this->setAttribute('position', $position);

        return $this;
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return \strlen($this->getAttribute('content'));
    }

    /**
     * @param string $data
     *
     * @return int
     */
    public function write(string $data): int
    {
        $content = $this->getAttribute('content');
        $content = \substr($content, 0, $this->getPosition());

        $content .= $data;
        $this->setAttribute('content', $content);

        $written = \strlen($data);
        $this->offsetPosition($written);

        return $written;
    }

    /**
     * @param int $offset
     *
     * @return int
     */
    protected function offsetPosition($offset): int
    {
        $contentSize = $this->size();
        $newPosition = $this->getPosition() + $offset;

        $newPosition = $contentSize < $newPosition ?
            $contentSize :
            $newPosition;

        $this->setPosition($newPosition);

        return $newPosition;
    }
}
