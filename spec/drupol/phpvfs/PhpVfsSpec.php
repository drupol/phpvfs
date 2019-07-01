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
        $vfs = new Filesystem('/');

        $this::register($vfs);

        $file = File::create('/a/b/c/d/foo.txt', 'foo');
        $vfs
            ->getCwd()
            ->add($file);

        $file = \fopen('phpvfs://a/b/c/d/foo.txt', 'r');
        $content = \fread($file, 1000);

        $this
            ->stream_eof()
            ->shouldReturn(true);

        \fclose($file);

        $this::unregister();
    }

    public function it_can_flush()
    {
        $vfs = new Filesystem('/');

        $file = File::create('/a/b/c/d/foo.txt');
        $vfs
            ->getCwd()
            ->add($file);

        $this::register($vfs);

        $this
            ->stream_flush()
            ->shouldReturn(true);

        $this::unregister();
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

        $this
            ->stream_eof()
            ->shouldReturn(true);

        \fclose($file);

        $this::fs()
            ->getCwd()
            ->exist('/a/b/c/d/foo.txt')
            ->shouldReturn(true);

        $this::fs()
            ->getCwd()
            ->get('/a/b/c/d/foo.txt')
            ->shouldBeAnInstanceOf(FileInterface::class);

        $file = \fopen('phpvfs://a/b/c/d/foo.txt', 'r');
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

        $this
            ->rename('phpvfs://d/e/f/g/bar.baz', 'phpvfs://a/b/c/d/foo.txt')
            ->shouldReturn(true);

        $this::unregister();
    }

    public function it_can_truncate()
    {
        $vfs = new Filesystem('/');

        $file = File::create('/a/b/c/d/foo.txt', 'foo');
        $vfs
            ->getCwd()
            ->add($file);

        $this::register($vfs);

        $fileHandler = \fopen('phpvfs://a/b/c/d/foo.txt', 'r');
        \ftruncate($fileHandler, 2);
        \rewind($fileHandler);

        $content = \fread($fileHandler, 1000);

        \rewind($fileHandler);

        $this
            ->stream_read(1000)
            ->shouldReturn($content);

        \rewind($fileHandler);

        $this
            ->stream_read(1000)
            ->shouldReturn('fo');

        $this
            ->stream_eof()
            ->shouldReturn(true);

        \ftruncate($fileHandler, 0);

        $this
            ->stream_eof()
            ->shouldReturn(true);

        \rewind($fileHandler);

        $this
            ->stream_read(1000)
            ->shouldReturn('');

        \fclose($fileHandler);

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

        $this
            ->rmdir('/a/b', 0)
            ->shouldReturn(true);

        $this::fs()
            ->getCwd()
            ->getPath()
            ->__toString()
            ->shouldBe('/a');

        $this::unregister();
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PhpVfs::class);
    }
}
