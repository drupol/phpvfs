<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Utils;

/**
 * Class Path.
 */
class Path implements PathInterface, \IteratorAggregate
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
     * @var string
     */
    private $scheme = '';

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if ('' !== $this->getScheme()) {
            $root = $this->getScheme() . '://';
        } else {
            $root = $this->isAbsolute() ? '/' : '';
        }

        return $root . \implode('/', $this->getFragments());
    }

    /**
     * {@inheritdoc}
     */
    public function basename(): string
    {
        return \basename(($this->isAbsolute() ? '/' : '') . \implode('/', $this->getFragments()));
    }

    /**
     * {@inheritdoc}
     */
    public function dirname(): string
    {
        return \dirname(($this->isAbsolute() ? '/' : '') . \implode('/', $this->getFragments()));
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString(string $id): Path
    {
        $instance = new self();

        if (false !== $parsed = \parse_url($id)) {
            if (\array_key_exists('scheme', $parsed)) {
                $instance->scheme = $parsed['scheme'];

                $id = \DIRECTORY_SEPARATOR . $parsed['host'] . $parsed['path'];
            }
        }

        $instance->absolute = 0 === \strpos($id, '/');

        $instance->fragments = \array_filter(
            \explode(
                \DIRECTORY_SEPARATOR,
                \ltrim($id, \DIRECTORY_SEPARATOR)
            )
        );

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstPart(): string
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
    public function getLastPart(): string
    {
        if (empty($this->fragments)) {
            return \DIRECTORY_SEPARATOR;
        }

        return \end($this->fragments);
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function isAbsolute(): bool
    {
        return $this->absolute;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot(): bool
    {
        return '' === $this->basename();
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(): bool
    {
        if (\preg_match('/^[^*?"<>|:]*$/', \trim((string) $this->withScheme(null), ' /'))) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function shift(): string
    {
        if (empty($this->fragments)) {
            return \DIRECTORY_SEPARATOR;
        }

        return \array_shift($this->fragments);
    }

    /**
     * {@inheritdoc}
     */
    public function withScheme(?string $scheme): Path
    {
        $clone = clone $this;

        $clone->scheme = $scheme ?? '';

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFragments(): array
    {
        $fragments = $this->fragments;

        if (empty($fragments[0])) {
            $fragments[0] = \DIRECTORY_SEPARATOR;
        }

        return $fragments;
    }
}
