<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Handler;

use drupol\phpvfs\Node\FileInterface as NodeFileInterface;

/**
 * Class File.
 *
 * @internal
 */
final class File implements FileInterface
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
    public function __construct(NodeFileInterface $file, string $mode, int $position = 0)
    {
        $this->file = $file;
        $this->mode = $mode;
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(): NodeFileInterface
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function isAppendable(): bool
    {
        $modeSplit = \str_split(\str_replace('b', '', $this->getMode()));

        return \in_array('a', $modeSplit, true);
    }

    /**
     * {@inheritdoc}
     */
    public function isExtended(): bool
    {
        $modeSplit = \str_split(\str_replace('b', '', $this->getMode()));

        return \in_array('+', $modeSplit, true);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable(): bool
    {
        $modeSplit = \str_split(\str_replace('b', '', $this->getMode()));

        return \in_array('r', $modeSplit, true);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable(): bool
    {
        $modeSplit = \str_split(\str_replace('b', '', $this->getMode()));

        return \in_array('w', $modeSplit, true);
    }

    /**
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    public function seekToEnd()
    {
        return $this->setPosition(
            \strlen($this->getFile()->getAttribute('content'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setMode(string $mode): File
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition(int $bytes): File
    {
        $this->position = $bytes;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function size(): int
    {
        return \strlen($this->getFile()->getAttribute('content'));
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    private function offsetPosition($offset): int
    {
        $contentSize = $this->size();
        $newPosition = $this->getPosition() + $offset;

        $newPosition = $contentSize < $newPosition ?
            $contentSize :
            $newPosition;

        $newPosition = (int) $newPosition;

        $this->setPosition($newPosition);

        return $newPosition;
    }
}
