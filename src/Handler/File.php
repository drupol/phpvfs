<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Handler;

use drupol\phpvfs\Node\FileInterface;

/**
 * Class File.
 */
class File
{
    /**
     * @var \drupol\phpvfs\Node\FileInterface
     */
    private $file;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var int
     */
    private $position;

    /**
     * File constructor.
     *
     * @param \drupol\phpvfs\Node\FileInterface $file
     * @param string $mode
     * @param int $position
     */
    public function __construct(FileInterface $file, string $mode, int $position = 0)
    {
        $this->file = $file;
        $this->mode = $mode;
        $this->position = $position;
    }

    public function getFile(): FileInterface
    {
        return $this->file;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function isAppendable(): bool
    {
        $modeSplit = \str_split(\str_replace('b', '', $this->getMode()));

        return \in_array('a', $modeSplit, true);
    }

    public function isExtended(): bool
    {
        $modeSplit = \str_split(\str_replace('b', '', $this->getMode()));

        return \in_array('+', $modeSplit, true);
    }

    public function isReadable(): bool
    {
        $modeSplit = \str_split(\str_replace('b', '', $this->getMode()));

        return \in_array('r', $modeSplit, true);
    }

    public function isWritable(): bool
    {
        $modeSplit = \str_split(\str_replace('b', '', $this->getMode()));

        return \in_array('w', $modeSplit, true);
    }

    /**
     * @param int $bytes
     *
     * @return string
     */
    public function read(int $bytes): string
    {
        $data = \substr(
            $this->getFile()->getAttribute('content'),
            $this->getPosition(),
            $bytes
        );

        $this->offsetPosition($bytes);

        return $data;
    }

    public function seekToEnd()
    {
        return $this->setPosition(
            \strlen($this->getFile()->getAttribute('content'))
        );
    }

    public function setMode(string $mode): File
    {
        $this->mode = $mode;

        return $this;
    }

    public function setPosition(int $bytes): File
    {
        $this->position = $bytes;

        return $this;
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return \strlen($this->getFile()->getAttribute('content'));
    }

    /**
     * @param int $bytes
     */
    public function truncate(int $bytes = 0)
    {
        $this->setPosition(0);
        $newData = \substr($this->getFile()->getAttribute('content'), 0, $bytes);

        if (\is_string($newData)) {
            $this->getFile()->setAttribute('content', $newData);
        }
    }

    /**
     * @param string $data
     *
     * @return int
     */
    public function write(string $data): int
    {
        $content = $this->getFile()->getAttribute('content');
        $content = \substr($content, 0, $this->getPosition());

        $content .= $data;
        $this->getFile()->setAttribute('content', $content);

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
