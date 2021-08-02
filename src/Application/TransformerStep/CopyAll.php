<?php

namespace AkeneoEtl\Application\TransformerStep;

use AkeneoEtl\Domain\TransformerStep;
use Closure;

class CopyAll implements TransformerStep
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

    public function transform(array $item, Closure $traceCallback = null): ?array
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
