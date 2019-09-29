<?php

declare(strict_types=1);

namespace drupol\phpvfs\Utils;

/**
 * Interface PathInterface.
 */
interface PathInterface
{
    /**
     * @return string
     */
    public function __toString();

    /**
     * @return string
     */
    public function basename(): string;

    /**
     * @return string
     */
    public function dirname(): string;

    /**
     * @param string $id
     *
     * @return \drupol\phpvfs\Utils\Path
     */
    public static function fromString(string $id): Path;

    /**
     * @return string
     */
    public function getFirstPart(): string;

    /**
     * @return mixed
     */
    public function getIterator();

    /**
     * @return string
     */
    public function getLastPart(): string;

    /**
     * @return string
     */
    public function getScheme(): string;

    /**
     * @return bool
     */
    public function isAbsolute(): bool;

    /**
     * @return bool
     */
    public function isRoot(): bool;

    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return string
     */
    public function shift(): string;

    /**
     * @param null|string $scheme
     *
     * @return \drupol\phpvfs\Utils\Path
     */
    public function withScheme(?string $scheme): Path;
}
