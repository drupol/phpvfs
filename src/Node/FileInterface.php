<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

interface FileInterface extends VfsInterface
{
    public function getPosition(): int;

    public function read(int $bytes): string;

    public function setPosition(int $position): FileInterface;

    public function write(string $data): int;
}
