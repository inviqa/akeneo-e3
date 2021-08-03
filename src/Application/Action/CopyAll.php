<?php

namespace AkeneoEtl\Application\Action;

use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Hook\ActionTraceHook;

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

    public function execute(array $item, ActionTraceHook $tracer = null): ?array
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
