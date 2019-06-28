<?php

declare(strict_types = 1);

namespace spec\drupol\phpvfs;

use drupol\phpvfs\Filesystem\Filesystem;
use drupol\phpvfs\Node\FileInterface;
use drupol\phpvfs\Node\File;
use drupol\phpvfs\PhpVfs;
use PhpSpec\ObjectBehavior;

class PhpVfsSpec extends ObjectBehavior
{
    public function it_can_open_and_read_write_a_file()
    {
        $vfs = new Filesystem('/');

        $this::register($vfs);

        $file = \fopen('phpvfs://a/b/c/d/foo.txt', 'w');
        \fwrite($file, 'bar');
        \fclose($file);

        $this::fs()
            ->exist('/a/b/c/d/foo.txt')
            ->shouldReturn(true);

        $this::fs()
            ->get('/a/b/c/d/foo.txt')
            ->shouldBeAnInstanceOf(FileInterface::class);

        $this::fs()
            ->inspect('/a/b/c/d/foo.txt')
            ->shouldReturn(File::class);

        $this::fs()
            ->get('/a/b/c/d/foo.txt')
            ->read(8192)
            ->shouldReturn('bar');

        \rename('phpvfs://a/b/c/d/foo.txt', 'phpvfs://d/e/f/g/bar.baz');

        $this::fs()
            ->get('/d/e/f/g/bar.baz')
            ->read(8192)
            ->shouldReturn('bar');

        \unlink('phpvfs://d/e/f/g/bar.baz');

        $this::fs()
            ->exist('/a/b/c/d/foo.txt')
            ->shouldReturn(false);

        $this::fs()
            ->exist('/d/e/f/g/bar.baz')
            ->shouldReturn(false);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PhpVfs::class);
    }
}
