<?php

declare(strict_types=1);

namespace drupol\phpvfs\Utils;

/**
 * Class Path.
 */
class Path implements \IteratorAggregate, PathInterface
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
        return \basename((string) $this);
    }

    /**
     * {@inheritdoc}
     */
    public function dirname(): string
    {
        return \dirname((string) $this);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString(string $id): self
    {
        $instance = new self();

        if (false !== $parsed = \parse_url($id)) {
            $parsed += [
                'path' => '',
                'host' => '',
            ];

            if (\array_key_exists('scheme', $parsed)) {
                $instance->scheme = $parsed['scheme'];

                $id = \DIRECTORY_SEPARATOR . $parsed['host'] . $parsed['path'];
            }
        }

        $instance->absolute = 0 === \mb_strpos($id, '/');

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

        return false === $first ?
            \DIRECTORY_SEPARATOR :
            $first;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        yield from $this->getFragments();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastPart(): string
    {
        if ([] === $this->fragments) {
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
        if (0 !== \preg_match('/^[^*?"<>|:]*$/', \trim((string) $this->withScheme(null), ' /'))) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function shift(): string
    {
        if ([] === $this->fragments) {
            return \DIRECTORY_SEPARATOR;
        }

        return \array_shift($this->fragments);
    }

    /**
     * {@inheritdoc}
     */
    public function withScheme(?string $scheme): self
    {
        $clone = clone $this;

        $clone->scheme = $scheme ?? '';

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    private function getFragments(): array
    {
        return $this->fragments;
    }
}
