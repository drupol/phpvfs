<?php

declare(strict_types = 1);

namespace spec\drupol\phpvfs\Filesystem;

use drupol\phpvfs\Filesystem\Filesystem;
use drupol\phpvfs\Node\DirectoryInterface;
use drupol\phpvfs\Node\FileInterface;
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
            ->cd('/')
            ->get('a')
            ->shouldBeAnInstanceOf(DirectoryInterface::class);

        $this
            ->shouldThrow(\Exception::class)
            ->during('cd', ['/e']);

        $this
            ->shouldThrow(\Exception::class)
            ->during('cd', ['e']);
    }

    public function it_can_get()
    {
        $this->beConstructedWith('/a/b/c/d');

        $this
            ->get('/a')
            ->shouldBeAnInstanceOf(DirectoryInterface::class);

        $this
            ->shouldThrow(\Exception::class)
            ->during('get', ['/c']);
    }

    public function it_can_touch()
    {
        $this->beConstructedWith('/a/b/c/d');

        $this
            ->touch('foo.txt')
            ->shouldReturn($this);

        $this
            ->get('/a/b/c/d/foo.txt')
            ->shouldBeAnInstanceOf(FileInterface::class);

        $this
            ->shouldThrow(\Exception::class)
            ->during('touch', ['/a/b/c/d/foo.txt']);
    }

    public function it_can_use_a_factory_method()
    {
        $this->beConstructedThrough('create', ['/a/b/c']);

        $this
            ->getCwd()
            ->getAttribute('id')
            ->shouldBe('c');
    }

    public function it_can_use_exists()
    {
        $this->beConstructedWith('/a/b/c/d');

        $this
            ->exist('/a/b/c/d')
            ->shouldReturn(true);

        $this
            ->exist('/')
            ->shouldReturn(true);

        $this
            ->exist('/a/b/c/d/e')
            ->shouldReturn(false);

        $this
            ->exist('/e')
            ->shouldReturn(false);
    }
    public function it_is_initializable()
    {
        $this->beConstructedWith('/');

        $this->shouldHaveType(Filesystem::class);
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
}
