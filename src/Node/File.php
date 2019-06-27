<?php

declare(strict_types = 1);

namespace drupol\phpvfs\Node;

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
        $absolute = false;
        if (0 === \strpos($id, \DIRECTORY_SEPARATOR, 0)) {
            $absolute = true;
        }

        if (\DIRECTORY_SEPARATOR !== $id && false !== \strpos($id, \DIRECTORY_SEPARATOR)) {
            $paths = \array_filter(\explode(\DIRECTORY_SEPARATOR, $id));

            if (true === $absolute) {
                $paths = \array_merge([\DIRECTORY_SEPARATOR], $paths);
            }

            $return = $root = self::create(\array_shift($paths));

            foreach ($paths as $path) {
                if (\end($paths) === $path) {
                    $child = self::create($path, $content, $attributes);
                } else {
                    $child = Directory::create($path);
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
