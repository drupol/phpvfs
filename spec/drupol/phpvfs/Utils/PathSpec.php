<?php

declare(strict_types=1);

namespace spec\drupol\phpvfs\Utils;

use drupol\phpvfs\Utils\Path;
use PhpSpec\ObjectBehavior;

class PathSpec extends ObjectBehavior
{
    public function it_can_be_constructed_with_a_path_trailing_slashes(): void
    {
        $this->beConstructedThrough('fromString', ['phpvfs://a/b/c/d/']);

        $this
            ->getScheme()
            ->shouldReturn('phpvfs');

        $this
            ->basename()
            ->shouldReturn('d');

        $this
            ->dirname()
            ->shouldReturn('phpvfs://a/b/c');

        $this
            ->isAbsolute()
            ->shouldReturn(true);

        $this
            ->getLastPart()
            ->shouldReturn('d');

        $this
            ->getFirstPart()
            ->shouldReturn('a');

        $this
            ->isRoot()
            ->shouldReturn(false);

        $this
            ->isValid()
            ->shouldReturn(true);

        $this
            ->shift();
    }

    public function it_can_be_constructed_with_path_having_scheme(): void
    {
        $this->beConstructedThrough('fromString', ['phpvfs://a/b/c/foo.txt']);

        $this
            ->getScheme()
            ->shouldReturn('phpvfs');

        $this
            ->basename()
            ->shouldReturn('foo.txt');

        $this
            ->dirname()
            ->shouldReturn('phpvfs://a/b/c');

        $this
            ->isAbsolute()
            ->shouldReturn(true);

        $this
            ->getLastPart()
            ->shouldReturn('foo.txt');

        $this
            ->getFirstPart()
            ->shouldReturn('a');

        $this
            ->isRoot()
            ->shouldReturn(false);

        $this
            ->isValid()
            ->shouldReturn(true);

        $this
            ->shift();
    }

    public function it_can_be_created_with_a_simple_root_dir(): void
    {
        $this->beConstructedThrough('fromString', ['/']);

        $this
            ->getScheme()
            ->shouldReturn('');

        $this
            ->basename()
            ->shouldReturn('');

        $this
            ->dirname()
            ->shouldReturn('/');

        $this
            ->isAbsolute()
            ->shouldReturn(true);

        $this
            ->getLastPart()
            ->shouldReturn('/');

        $this
            ->getFirstPart()
            ->shouldReturn('/');

        $this
            ->isRoot()
            ->shouldReturn(true);

        $this
            ->isValid()
            ->shouldReturn(true);
    }

    public function it_can_be_created_with_an_absolute_path(): void
    {
        $this->beConstructedThrough('fromString', ['/a/b/c/foo.txt']);

        $this
            ->getScheme()
            ->shouldReturn('');

        $this
            ->basename()
            ->shouldReturn('foo.txt');

        $this
            ->dirname()
            ->shouldReturn('/a/b/c');

        $this
            ->isAbsolute()
            ->shouldReturn(true);

        $this
            ->getLastPart()
            ->shouldReturn('foo.txt');

        $this
            ->getFirstPart()
            ->shouldReturn('a');

        $this
            ->isRoot()
            ->shouldReturn(false);

        $this
            ->isValid()
            ->shouldReturn(true);

        $this
            ->shift()
            ->shouldReturn('a');

        $this
            ->shift()
            ->shouldReturn('b');

        $this
            ->shift()
            ->shouldReturn('c');

        $this
            ->shift()
            ->shouldReturn('foo.txt');

        $this
            ->shift()
            ->shouldReturn('/');

        $this
            ->shift()
            ->shouldReturn('/');
    }

    public function it_can_detect_if_a_path_is_valid_or_not(): void
    {
        $this->beConstructedThrough('fromString', ['/ij \lkjf \o k/ kjdf/ lkjd/-lkj"+']);

        $this
            ->isValid()
            ->shouldReturn(false);
    }

    public function it_can_detect_if_a_path_with_scheme_is_valid_or_not(): void
    {
        $this->beConstructedThrough('fromString', ['phpvfs://// lkjd/-lkj"+']);

        $this
            ->isValid()
            ->shouldReturn(false);
    }

    public function it_can_return_a_clone_with_another_scheme(): void
    {
        $this->beConstructedThrough('fromString', ['/a/b/c/foo.txt']);

        $this
            ->withScheme('foo')
            ->__toString()
            ->shouldReturn('foo://a/b/c/foo.txt');

        $this
            ->withScheme('bar')
            ->__toString()
            ->shouldReturn('bar://a/b/c/foo.txt');

        $this
            ->withScheme(null)
            ->__toString()
            ->shouldReturn('/a/b/c/foo.txt');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Path::class);
    }
}
