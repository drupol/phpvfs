<?php

declare(strict_types = 1);

namespace drupol\phpvfs\StreamWrapper\Handler;

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
        $modeSplit = str_split(str_replace('b', '', $this->getMode()));

        return \in_array('a', $modeSplit, true);
    }

    /**
     * {@inheritdoc}
     */
    public function isExtended(): bool
    {
        $modeSplit = str_split(str_replace('b', '', $this->getMode()));

        return \in_array('+', $modeSplit, true);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable(): bool
    {
        $modeSplit = str_split(str_replace('b', '', $this->getMode()));

        return \in_array('r', $modeSplit, true);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable(): bool
    {
        $modeSplit = str_split(str_replace('b', '', $this->getMode()));

        return \in_array('w', $modeSplit, true);
    }

    /**
     * {@inheritdoc}
     */
    public function read(int $bytes): string
    {
        $data = mb_substr(
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
            mb_strlen($this->getFile()->getAttribute('content'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition(int $bytes): self
    {
        $this->position = $bytes;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function size(): int
    {
        return mb_strlen($this->getFile()->getAttribute('content'));
    }

    /**
     * {@inheritdoc}
     */
    public function truncate(int $bytes = 0): self
    {
        $this->setPosition($bytes);

        $this->getFile()->setAttribute(
            'content',
            mb_substr($this->getFile()->getAttribute('content'), 0, $bytes)
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $data): int
    {
        $content = $this->getFile()->getAttribute('content');
        $content = mb_substr($content, 0, $this->getPosition());

        $content .= $data;
        $this->getFile()->setAttribute('content', $content);

        $written = mb_strlen($data);
        $this->offsetPosition($written);

        return $written;
    }

    /**
     * {@inheritdoc}
     */
    private function offsetPosition(int $offset): int
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
