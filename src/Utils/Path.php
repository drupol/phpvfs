<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Utils;

class Path implements \IteratorAggregate
{
    /**
     * @var string[]
     */
    private $fragments;

    /**
     * @var string
     */
    private $path;

    /**
     * @var bool
     */
    private $absolute;

    public function __toString()
    {
        return ($this->isAbsolute() ? '/' : '') . implode('/', $this->getFragments());
    }

    /**
     * @param string $id
     *
     * @return \drupol\phpvfs\Utils\Path
     */
    public static function fromString(string $id)
    {
        $instance = new self();
        $instance->fragments = \explode(
            \DIRECTORY_SEPARATOR,
            \ltrim($id, DIRECTORY_SEPARATOR)
        );
        $instance->absolute = \strpos($id, '/') === 0;


        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstPart()
    {
        $first = \reset($this->fragments);

        return empty($first) ?
            DIRECTORY_SEPARATOR :
            $first;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        yield from $this->getFragments();
    }

    protected function getFragments()
    {
        $fragments = $this->fragments;

        if (empty($fragments[0])) {
            $fragments[0] = DIRECTORY_SEPARATOR;
        }

        return $fragments;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastPart()
    {
        return \end($this->fragments);
    }

    public function isAbsolute()
    {
        return $this->absolute;
    }

    public function isValid()
    {
        return \preg_match('/^[^*?"<>|:]*$/', \trim($this->__toString(), ' /'));
    }

    public function shift()
    {
        return \array_shift($this->fragments);
    }
}
