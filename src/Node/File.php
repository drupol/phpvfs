<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

use drupol\phpvfs\Utils\Path;

class File extends Vfs implements FileInterface
{
    /**
     * @param string $id
     * @param null|string $content
     * @param array $attributes
     *
     * @throws \Exception
     *
     * @return \drupol\phpvfs\Node\File
     */
    public static function create(string $id, string $content = null, array $attributes = [])
    {
        $path = Path::fromString($id);

        if (\DIRECTORY_SEPARATOR !== $id && false !== \strpos($id, \DIRECTORY_SEPARATOR)) {
            if ($path->isAbsolute()) {
                $firstPart = \DIRECTORY_SEPARATOR;
            } else {
                $firstPart = $path->shift();
            }

            $return = $root = self::create($firstPart, $content, $attributes);

            foreach ($path->getIterator() as $pathPart) {
                if ($path->getLastPart() === $pathPart) {
                    $child = self::create($pathPart, $content, $attributes);
                } else {
                    $child = Directory::create($pathPart);
                }
                $root->add($child);
                $root = $child;
            }

            return $return;
        }

        $attributes = [
            'id' => $id,
            'content' => $content,
        ] + $attributes;

        return new self($attributes);
    }
}
