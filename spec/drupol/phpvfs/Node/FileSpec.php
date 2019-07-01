<?php

declare(strict_types = 1);

namespace spec\drupol\phpvfs\Node;

use drupol\phpvfs\Node\File;
use PhpSpec\ObjectBehavior;

class FileSpec extends ObjectBehavior
{
    public function it_can_be_todotada()
    {
        $this->beConstructedThrough('create', ['a/b/c/d/e/file.txt', 'content']);

        $this
            ->root()
            ->getAttribute('id')
            ->shouldReturn('a');

        $this
            ->getAttribute('id')
            ->shouldReturn('file.txt');

        $this
            ->read()
            ->shouldReturn('content');

        $this
            ->count()
            ->shouldReturn(0);

        $this
            ->root()
            ->count()
            ->shouldReturn(5);

        $file = File::create('a/b/file.txt');

        $this
            ->root()
            ->add($file);

        $this
            ->root()
            ->count()
            ->shouldReturn(6);
    }

    public function it_can_be_written()
    {
        $this->beConstructedThrough('create', ['/a/b/file.txt']);

        $this
            ->root()
            ->count()
            ->shouldReturn(3);

        $this
            ->read()
            ->shouldReturn('');

        $this
            ->write('content')
            ->shouldReturn($this);

        $this
            ->read()
            ->shouldReturn('content');
    }

    public function it_get_its_path()
    {
        $this->beConstructedThrough('create', ['/a/b/file.txt']);

        $this
            ->getPath()
            ->__toString()
            ->shouldReturn('/a/b/file.txt');

        $this
            ->getPath()
            ->isAbsolute()
            ->shouldReturn(true);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(File::class);
    }
}
