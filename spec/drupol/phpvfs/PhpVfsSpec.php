<?php

declare(strict_types = 1);

namespace spec\drupol\phpvfs;

use drupol\phpvfs\Filesystem\Filesystem;
use drupol\phpvfs\Node\DirectoryInterface;
use drupol\phpvfs\Node\FileInterface;
use drupol\phpvfs\Node\File;
use drupol\phpvfs\PhpVfs;
use PhpSpec\ObjectBehavior;

class PhpVfsSpec extends ObjectBehavior
{
    public function it_can_delete_a_file()
    {
        $vfs = new Filesystem('/');

        $this::register($vfs);

        $file = File::create('/a/b/c/d/foo.txt');
        $vfs
            ->getCwd()
            ->add($file);

        $file = \fopen('phpvfs://a/b/c/d/foo.txt', 'w');
        \fwrite($file, 'bar');
        \fclose($file);

        \unlink('phpvfs://a/b/c/d/foo.txt');

        $this::fs()
            ->getCwd()
            ->exist('/a/b/c/d/foo.txt')
            ->shouldReturn(false);

        $this::unregister();
    }

    public function it_can_do_stream_eof()
    {
        $this
            ->stream_eof()
            ->shouldReturn(true);
    }

    public function it_can_get_a_node()
    {
        $vfs = new Filesystem('/');

        $file = File::create('/a/b/c/d/foo.txt');
        $vfs
            ->getCwd()
            ->add($file);

        $this::register($vfs);

        $file = \fopen('phpvfs://a/b/c/d/foo.txt', 'w');
        \fwrite($file, 'bar');
        \fclose($file);

        $this::fs()
            ->getCwd()
            ->get('/a/b/c/d/foo.txt')
            ->shouldBeAnInstanceOf(FileInterface::class);

        $this::fs()
            ->getCwd()
            ->get('/a/b/c/d')
            ->shouldBeAnInstanceOf(DirectoryInterface::class);

        $this::unregister();
    }

    public function it_can_open_and_read_write_a_file()
    {
        $vfs = new Filesystem('/');

        $file = File::create('/a/b/c/d/foo.txt');
        $vfs
            ->getCwd()
            ->add($file);

        $this::register($vfs);

        $file = \fopen('phpvfs://a/b/c/d/foo.txt', 'w');
        \fwrite($file, 'bar');
        \fclose($file);

        $this::fs()
            ->getCwd()
            ->exist('/a/b/c/d/foo.txt')
            ->shouldReturn(true);

        $this::fs()
            ->getCwd()
            ->get('/a/b/c/d/foo.txt')
            ->shouldBeAnInstanceOf(FileInterface::class);

        $file = \fopen('phpvfs://a/b/c/d/foo.txt', 'w');
        \fwrite($file, 'foo');
        \fclose($file);

        $this::unregister();
    }

    public function it_can_rename_a_file()
    {
        $vfs = new Filesystem('/');

        $file = File::create('/a/b/c/d/foo.txt');
        $vfs
            ->getCwd()
            ->add($file);

        $this::register($vfs);

        $file = \fopen('phpvfs://a/b/c/d/foo.txt', 'w');
        \fwrite($file, 'bar');
        \fclose($file);

        $this::fs()
            ->getCwd()
            ->get('/a/b/c/d/foo.txt')
            ->read(8192)
            ->shouldReturn('bar');

        \rename('phpvfs://a/b/c/d/foo.txt', 'phpvfs://d/e/f/g/bar.baz');

        $this::fs()
            ->getCwd()
            ->get('/d/e/f/g/bar.baz')
            ->read(8192)
            ->shouldReturn('bar');

        $this
            ->shouldThrow(\Exception::class)
            ->during('rename', ['phpvfs://foo.txt', 'phpvfs://d/e/f/g/bar.baz']);

        $this
            ->shouldThrow(\Exception::class)
            ->during('rename', ['phpvfs://d/e/f/g/bar.baz', 'phpvfs://d/e/f/g/bar.baz']);

        $this::unregister();
    }

    public function it_can_use_stream_stat()
    {
        $vfs = new Filesystem('/');

        $file = File::create('/a/b/c/d/foo.txt');
        $vfs
            ->getCwd()
            ->add($file);

        $this::register($vfs);

        $fileHandler = \fopen('phpvfs://a/b/c/d/foo.txt', 'w');
        \fwrite($fileHandler, 'bar');
        \fclose($fileHandler);

        $this
            ->stream_stat()
            ->shouldReturn([]);

        // @wtf: If you remove $fileHandler, the test fails.
        $fileHandler = \fopen('phpvfs://a/b/c/d/foo.txt', 'w');
        $this
            ->stream_stat()
            ->shouldReturn((array) $file->getAttributes());

        $this::unregister();
    }

    public function it_is_able_to_remove_directory()
    {
        $vfs = new Filesystem('/');

        $file = File::create('/a/b/c/d/foo.txt');
        $vfs
            ->getCwd()
            ->add($file);

        $this::register($vfs);

        \rmdir('phpvfs://a/b/c/d');

        $this::fs()
            ->getCwd()
            ->getPath()
            ->__toString()
            ->shouldBe('/a/b/c');

        $this::unregister();
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PhpVfs::class);
    }
}
