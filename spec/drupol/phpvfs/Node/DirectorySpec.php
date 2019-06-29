<?php

declare(strict_types = 1);

namespace spec\drupol\phpvfs\Node;

use drupol\phpvfs\Node\Directory;
use drupol\phpvfs\Node\File;
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
    }
    public function it_is_initializable()
    {
        $this->shouldHaveType(Directory::class);
    }
}
