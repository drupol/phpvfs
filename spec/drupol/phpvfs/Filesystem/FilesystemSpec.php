<?php

declare(strict_types = 1);

namespace spec\drupol\phpvfs\Filesystem;

use drupol\phpvfs\Filesystem\Filesystem;
use drupol\phpvfs\Node\Directory;
use drupol\phpvfs\Node\DirectoryInterface;
use PhpSpec\ObjectBehavior;

class FilesystemSpec extends ObjectBehavior
{
    public function it_can_be_created_with_more_directories()
    {
        $this->beConstructedWith(Directory::create('/a/b/c/d'));

        $this
            ->root()
            ->getAttribute('id')
            ->shouldReturn('/');

        $this
            ->getCwd()
            ->getAttribute('id')
            ->shouldReturn('d');

        $this
            ->pwd()
            ->shouldReturn('/a/b/c/d');
    }

    public function it_can_be_created_with_root_only()
    {
        $this->beConstructedWith(Directory::create('/'));

        $this
            ->root()
            ->getAttribute('id')
            ->shouldReturn('/');

        $this
            ->pwd()
            ->shouldReturn('/');
    }

    public function it_can_change_directory()
    {
        $this->beConstructedWith(Directory::create('/a/b/c/d'));

        $this
            ->getCwd()
            ->cd('/')
            ->shouldBeAnInstanceOf(DirectoryInterface::class);

        $this
            ->pwd()
            ->shouldReturn('/a/b/c/d');
    }

    public function it_can_set_and_get_its_cwd()
    {
        $this->beConstructedWith(Directory::create('/a/b/c/d'));

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

        $this
            ->pwd()
            ->shouldReturn('/a/b/c');
    }

    public function it_is_initializable()
    {
        $this->beConstructedWith(Directory::create('/'));

        $this->shouldHaveType(Filesystem::class);
    }
}
