<?php

declare(strict_types = 1);

namespace spec\drupol\phpvfs\StreamWrapper;

use drupol\phpvfs\Filesystem\Filesystem;
use drupol\phpvfs\Node\Directory;
use drupol\phpvfs\Node\DirectoryInterface;
use drupol\phpvfs\Node\FileInterface;
use drupol\phpvfs\Node\File;
use drupol\phpvfs\StreamWrapper\PhpVfs;
use PhpSpec\ObjectBehavior;

class PhpVfsSpec extends ObjectBehavior
{
    public function it_can_delete_a_file()
    {
        $file = File::create('/a/b/c/d/foo.txt');
        $this::fs()
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
    }

    public function it_can_do_stream_eof()
    {
        $file = File::create('/a/b/c/d/foo.txt', 'foo');
        $this::fs()
            ->getCwd()
            ->add($file);

        $file = \fopen('phpvfs://a/b/c/d/foo.txt', 'r');
        $content = \fread($file, 1000);

        $this
            ->stream_eof()
            ->shouldReturn(true);

        \fclose($file);
    }

    public function it_can_flush()
    {
        $file = File::create('/a/b/c/d/foo.txt');
        $this::fs()
            ->getCwd()
            ->add($file);

        $this
            ->stream_flush()
            ->shouldReturn(true);
    }

    public function it_can_get_a_node()
    {
        $file = File::create('/a/b/c/d/foo.txt');
        $this::fs()
            ->getCwd()
            ->add($file);

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
    }

    public function it_can_open_and_read_write_a_file()
    {
        $file = File::create('/a/b/c/d/foo.txt');
        $this::fs()
            ->getCwd()
            ->add($file);

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
    }

    public function it_can_rename_a_file()
    {
        $file = File::create('/a/b/c/d/foo.txt');
        $this::fs()
            ->getCwd()
            ->add($file);

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
    }

    public function it_can_seek()
    {
        $file = File::create('/a/b/c/d/foo.txt', 'abc');
        $this::fs()
            ->getCwd()
            ->add($file);

        $fileHandler = \fopen('phpvfs://a/b/c/d/foo.txt', 'r');

        $this
            ->stream_seek(2)
            ->shouldReturn(true);

        $this
            ->stream_read(1)
            ->shouldReturn('c');

        \fclose($fileHandler);
    }

    public function it_can_truncate()
    {
        $file = File::create('/a/b/c/d/foo.txt', 'foo');
        $this::fs()
            ->getCwd()
            ->add($file);

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
    }

    public function it_can_use_stream_stat()
    {
        $file = File::create('/a/b/c/d/foo.txt');
        $this::fs()
            ->getCwd()
            ->add($file);

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
    }

    public function it_is_able_to_remove_directory()
    {
        $file = File::create('/a/b/c/d/foo.txt');
        $this::fs()
            ->getCwd()
            ->add($file);

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
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PhpVfs::class);
    }
    public function let()
    {
        $vfs = new Filesystem(Directory::create('/'));

        $this::register($vfs);
    }

    public function letgo()
    {
        $this::unregister();
    }
}
