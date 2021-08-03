<?php

namespace AkeneoEtl\Domain;

use AkeneoEtl\Domain\Hook\ActionTraceHook;

interface Action
{
    public function getType(): string;

    public function execute(array $item, ActionTraceHook $tracer = null): ?array;
}
