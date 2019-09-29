<?php

declare(strict_types=1);

namespace spec\drupol\phpvfs\StreamWrapper\Handler;

use drupol\phpvfs\StreamWrapper\Handler\File;
use PhpSpec\ObjectBehavior;

class FileSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $file = \drupol\phpvfs\Node\File::create('foo.txt', 'barbaz');

        $this->beConstructedWith($file, 'r');

        $this->shouldHaveType(File::class);

        $this
            ->getFile()
            ->shouldReturn($file);

        $this
            ->getMode()
            ->shouldReturn('r');

        $this
            ->getPosition()
            ->shouldReturn(0);

        $this
            ->isReadable()
            ->shouldReturn(true);

        $this
            ->isWritable()
            ->shouldReturn(false);

        $this
            ->isAppendable()
            ->shouldReturn(false);

        $this
            ->isExtended()
            ->shouldReturn(false);

        $this
            ->size()
            ->shouldReturn(6);

        $this
            ->read(1000)
            ->shouldReturn('barbaz');

        $this
            ->getPosition()
            ->shouldReturn(6);

        $this
            ->setPosition(0)
            ->seekToEnd()
            ->getPosition()
            ->shouldReturn(6);

        $this
            ->truncate(5)
            ->getPosition()
            ->shouldReturn(5);

        $this
            ->setPosition(0)
            ->getPosition()
            ->shouldReturn(0);

        $this
            ->read(1000)
            ->shouldReturn('barba');

        $this
            ->truncate()
            ->getPosition()
            ->shouldReturn(0);

        $this
            ->read(1000)
            ->shouldReturn('');
    }
}
