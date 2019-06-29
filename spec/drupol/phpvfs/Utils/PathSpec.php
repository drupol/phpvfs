<?php

declare(strict_types = 1);

namespace spec\drupol\phpvfs\Utils;

use drupol\phpvfs\Utils\Path;
use PhpSpec\ObjectBehavior;

class PathSpec extends ObjectBehavior
{
    public function it_can_be_created_with_a_simple_root_dir()
    {
        $this->beConstructedThrough('fromString', ['/']);

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
            ->shouldReturn('');

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

    public function it_can_be_created_with_an_absolute_path()
    {
        $this->beConstructedThrough('fromString', ['/a/b/c/foo.txt']);

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
    }

    public function it_can_detect_if_a_path_is_valid_or_not()
    {
        $this->beConstructedThrough('fromString', ['/ij \lkjf \o k/ kjdf/ lkjd/-lkj"+']);

        $this
            ->isValid()
            ->shouldReturn(false);
    }
    public function it_is_initializable()
    {
        $this->shouldHaveType(Path::class);
    }
}
