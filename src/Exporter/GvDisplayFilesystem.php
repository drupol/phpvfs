<?php

namespace drupol\phpvfs\Exporter;

use drupol\phptree\Exporter\GvConvert;
use drupol\phptree\Node\NodeInterface;
use drupol\phpvfs\Utils\Path;

/**
 * Class GvDisplayFilesystem.
 */
class GvDisplayFilesystem extends GvConvert
{
    /**
     * {@inheritdoc}
     */
    protected function getNodeAttributes(NodeInterface $node): array
    {
        $attributes = parent::getNodeAttributes($node);

        if (!\array_key_exists('label', $attributes)) {
            $attributes['label'] = Path::fromString($attributes['id']);
        }

        return $attributes;
    }

}
