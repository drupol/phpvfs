<?php

declare(strict_types = 1);

namespace spec\drupol\phpvfs\Node;

use drupol\phptree\Node\AttributeNode;
use drupol\phpvfs\Node\Directory;
use drupol\phpvfs\Node\DirectoryInterface;
use drupol\phpvfs\Node\File;
use drupol\phpvfs\Node\FileInterface;
use PhpSpec\ObjectBehavior;

class DirectorySpec extends ObjectBehavior
{
    public function it_can_add_other_nodes_type()
    {
        $this->beConstructedThrough('create', ['a/b/c/d/e']);

        $this
            ->root()
            ->getAttribute('id')
            ->shouldReturn('a');

        $this
            ->getAttribute('id')
            ->shouldReturn('e');

        $this
            ->mkdir('f/g/h')
            ->shouldReturn($this);

        $this
            ->count()
            ->shouldReturn(3);

        $this
            ->root()
            ->count()
            ->shouldReturn(7);

        $file = File::create('a/b/file.txt');

        $this
            ->root()
            ->add($file);

        $this
            ->root()
            ->count()
            ->shouldReturn(8);

        $differentNodeTypeNotExtendingVfs = new class() extends AttributeNode {
        };

        $this
            ->shouldThrow(\Exception::class)
            ->during('add', [$differentNodeTypeNotExtendingVfs]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Directory::class);
    }

    public function it_can_remove_directory_absolute()
    {
        $this->beConstructedThrough('create', ['/a/b/c/d/e']);

        $this
            ->rmdir('/a/b/c/d/e');

        $this
            ->root()
            ->getAttribute('id')
            ->shouldReturn('/');

        $this
            ->count()
            ->shouldReturn(0);
    }

    public function it_can_remove_directory_relative() {
        $this->beConstructedThrough('create', ['/a/b/c/d/e']);

        $dirs = Directory::create('f/g/h');

        $this->add($dirs);

        $this
            ->rmdir('f/g');

        $this
            ->containsAttributeId('f')
            ->shouldBeAnInstanceOf(DirectoryInterface::class);

        $this
            ->containsAttributeId('g')
            ->shouldBeNull();
    }

    public function it_can_remove_directory_containing_files() {
        $this->beConstructedThrough('create', ['/a/b/c/d/e']);

        $files = [
            File::create('foo.txt'),
            File::create('bar.txt'),
            File::create('baz.txt'),
        ];

        $this->add(...$files);

        $this
            ->rmdir('foo.txt');

        $this
            ->containsAttributeId('foo.txt')
            ->shouldBeAnInstanceOf(FileInterface::class);
    }

    public function it_can_get_a_subdirectory() {
        $this->beConstructedThrough('create', ['/a/b/c/d/e']);

        $this
            ->get('/a/b/c/d/e')
            ->shouldBeAnInstanceOf(DirectoryInterface::class);
        $this
            ->get('/a/b/c/d')
            ->shouldBeAnInstanceOf(DirectoryInterface::class);
        $this
            ->get('/a/b/c')
            ->shouldBeAnInstanceOf(DirectoryInterface::class);
        $this
            ->get('/a/b')
            ->shouldBeAnInstanceOf(DirectoryInterface::class);
        $this
            ->get('/a')
            ->shouldBeAnInstanceOf(DirectoryInterface::class);
        $this
            ->get('/')
            ->shouldBeAnInstanceOf(DirectoryInterface::class);

        $file = File::create('f/foo.txt');

        $this->add($file);

        $this
            ->get('/a/b/c/d/e/f/foo.txt')
            ->shouldBeAnInstanceOf(FileInterface::class);
    }
}
