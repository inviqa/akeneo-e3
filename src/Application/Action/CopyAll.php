<?php

namespace AkeneoEtl\Application\Action;

use AkeneoEtl\Domain\Action;
use Closure;

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

    public function execute(array $item, Closure $traceCallback = null): ?array
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
