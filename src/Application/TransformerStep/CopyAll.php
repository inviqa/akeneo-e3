<?php

namespace AkeneoEtl\Application\TransformerStep;

use AkeneoEtl\Domain\TransformerStep;

class CopyAll implements TransformerStep
{
    public function __construct(array $options)
    {
    }

    public function getType(): string
    {
        return 'copy-all';
    }

    public function transform(array $item): ?array
    {
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
