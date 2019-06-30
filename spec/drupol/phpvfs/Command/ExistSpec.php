<?php

declare(strict_types = 1);

namespace spec\drupol\phpvfs\Command;

use drupol\phpvfs\Filesystem\Filesystem;
use drupol\phpvfs\Node\File;
use PhpSpec\ObjectBehavior;

class ExistSpec extends ObjectBehavior
{
    public function it_can_check_if_a_vfs_component_exist()
    {
        $vfs = new Filesystem('/');

        $file = File::create('/a/b/c/d/foo.txt');
        $vfs
            ->getCwd()
            ->add($file);

        $file = File::create('/a/b/bar.txt');
        $vfs
            ->getCwd()
            ->add($file);

        $file = File::create('/d/e/baz.txt');
        $vfs
            ->getCwd()
            ->add($file);

        $this::exec($vfs, '/a/b/c/d/foo.txt', '/a/b/bar.txt', '/d/e/baz.txt')
            ->shouldReturn(true);

        $this::exec($vfs, '/zzzz')
            ->shouldReturn(false);

        $this::exec($vfs, '/a/b/c/d/foo.txt', '/zzzz')
            ->shouldReturn(false);

        $this::exec($vfs, '/')
            ->shouldReturn(true);

        $this::exec($vfs, '/a/b/c/d', '/a/b', '/d/e')
            ->shouldReturn(true);

        $this::exec($vfs, '/a/b/c/d', '/zzzz')
            ->shouldReturn(false);
    }
}
