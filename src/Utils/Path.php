<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Utils;

class Path implements \IteratorAggregate
{
    /**
     * @var bool
     */
    private $absolute;
    /**
     * @var string[]
     */
    private $fragments;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return ($this->isAbsolute() ? '/' : '') . \implode('/', $this->getFragments());
    }

    public function basename()
    {
        return \basename(($this->isAbsolute() ? '/' : '') . \implode('/', $this->getFragments()));
    }

    public function dirname()
    {
        return \dirname(($this->isAbsolute() ? '/' : '') . \implode('/', $this->getFragments()));
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString(string $id)
    {
        $instance = new self();
        $instance->fragments = \explode(
            \DIRECTORY_SEPARATOR,
            \ltrim($id, \DIRECTORY_SEPARATOR)
        );
        $instance->absolute = 0 === \strpos($id, '/');

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstPart()
    {
        $first = \reset($this->fragments);

        return empty($first) ?
            \DIRECTORY_SEPARATOR :
            $first;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        yield from $this->getFragments();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastPart()
    {
        return \end($this->fragments);
    }

    /**
     * {@inheritdoc}
     */
    public function isAbsolute()
    {
        return $this->absolute;
    }

    public function isRoot()
    {
        return '' === $this->basename();
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        if (\preg_match('/^[^*?"<>|:]*$/', \trim($this->__toString(), ' /'))) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function shift()
    {
        return \array_shift($this->fragments);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFragments()
    {
        $fragments = $this->fragments;

        if (empty($fragments[0])) {
            $fragments[0] = \DIRECTORY_SEPARATOR;
        }

        return $fragments;
    }
}
