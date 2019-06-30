<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Utils;

interface PathInterface
{
    public function __toString();

    /**
     * @return string
     */
    public function basename(): string;

    /**
     * @return string
     */
    public function dirname(): string;

    public static function fromString(string $id): Path;

    public function getFirstPart(): string;

    public function getIterator();

    public function getLastPart(): string;

    public function isAbsolute(): bool;

    /**
     * @return bool
     */
    public function isRoot(): bool;

    public function isValid(): bool;

    public function shift(): string;

    /**
     * @param null|string $scheme
     *
     * @return \drupol\phpvfs\Utils\Path
     */
    public function withScheme(?string $scheme): Path;
}
