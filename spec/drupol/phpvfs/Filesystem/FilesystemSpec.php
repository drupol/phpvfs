<?php

declare(strict_types = 1);

namespace spec\drupol\phpvfs\Filesystem;

use drupol\phpvfs\Filesystem\Filesystem;
use drupol\phpvfs\Node\DirectoryInterface;
use PhpSpec\ObjectBehavior;

class FilesystemSpec extends ObjectBehavior
{
    public function it_can_be_created_with_more_directories()
    {
        $this->beConstructedWith('/a/b/c/d');

        $this
            ->root()
            ->getAttribute('id')
            ->shouldReturn('/');

        $this
            ->getCwd()
            ->getAttribute('id')
            ->shouldReturn('d');
    }

    public function it_can_be_created_with_root_only()
    {
        $this->beConstructedWith('/');

        $this
            ->root()
            ->getAttribute('id')
            ->shouldReturn('/');
    }

    public function it_can_change_directory()
    {
        $this->beConstructedWith('/a/b/c/d');

        $this
            ->getCwd()
            ->cd('/')
            ->shouldBeAnInstanceOf(DirectoryInterface::class);
    }

    public function it_can_set_and_get_its_cwd()
    {
        $this->beConstructedWith('/a/b/c/d');

        $this
            ->getCwd()
            ->getAttribute('id')
            ->shouldReturn('d');

        $cwd = $this->getCwd();

        $cwd = $cwd->getParent();

        $this
            ->setCwd($cwd)
            ->shouldReturn($this);

        $this
            ->getCwd()
            ->getAttribute('id')
            ->shouldReturn('c');
    }

    public function it_can_use_a_factory_method()
    {
        $this->beConstructedThrough('create', ['/a/b/c']);

        $this
            ->getCwd()
            ->getAttribute('id')
            ->shouldBe('c');
    }

    public function it_is_initializable()
    {
        $this->beConstructedWith('/');

        $this->shouldHaveType(Filesystem::class);
    }
}
