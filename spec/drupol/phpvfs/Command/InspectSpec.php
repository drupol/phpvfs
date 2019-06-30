<?php

declare(strict_types = 1);

namespace spec\drupol\phpvfs\Command;

use drupol\phpvfs\Filesystem\Filesystem;
use drupol\phpvfs\Node\File;
use PhpSpec\ObjectBehavior;

class InspectSpec extends ObjectBehavior
{
    public function it_can_inspect_an_item()
    {
        $vfs = new Filesystem('/');

        $file = File::create('/a/b/c/d/foo.txt');
        $vfs
            ->getCwd()
            ->add($file);

        $this::exec($vfs, '/a/b/c/d/foo.txt')
            ->shouldReturn('drupol\phpvfs\Node\File');

        $this::exec($vfs, '/a/b/c/d')
            ->shouldReturn('drupol\phpvfs\Node\Directory');

        $this
            ->shouldThrow(\Exception::class)
            ->during('exec', [$vfs, '/unexistant/path']);
    }
}
