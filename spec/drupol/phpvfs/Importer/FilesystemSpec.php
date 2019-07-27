<?php

declare(strict_types = 1);

namespace spec\drupol\phpvfs\Importer;

use drupol\phpvfs\Importer\Filesystem;
use drupol\phpvfs\Node\DirectoryInterface;
use PhpSpec\ObjectBehavior;

class FilesystemSpec extends ObjectBehavior
{
    public function it_can_import_a_real_filesystem(): void
    {
        $this
            ->import(__DIR__)
            ->shouldReturnAnInstanceOf(DirectoryInterface::class);

        $this
            ->import(__DIR__)
            ->count()
            ->shouldReturn(1);

        $this
            ->import(__DIR__)
            ->getAttribute('id')
            ->shouldBe($this->import(__DIR__)->getAttribute('label'));

        $this
            ->import(__DIR__)
            ->getAttribute('shape')
            ->shouldBe('square');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Filesystem::class);
    }

    public function it_should_throw_an_error(): void
    {
        $this
            ->shouldThrow(\Exception::class)
            ->during('import', ['abracadabra']);

        $this
            ->shouldThrow(\Exception::class)
            ->during('import', [['wrong argument type']]);
    }
}
