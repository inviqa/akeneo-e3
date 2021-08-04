<?php

namespace AkeneoEtl\Application\Action;

use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Resource;

class CopyAll implements Action
{
    private array $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function getType(): string
    {
        return 'copy-all';
    }

    public function execute(Resource $resource): ?array
    {
        $item = $resource->toArray();

        if (isset($item['parent']) === true) {
            return null;
        }

        $item['family'] = 'common';
        $item['categories'] = ['master'];
        $item['values'] = [];
        $item['associations'] = [];

        unset($item['quantified_associations']);

        return $item;
    }
}
