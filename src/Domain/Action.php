<?php

namespace AkeneoEtl\Domain;

use Closure;

interface Action
{
    public function getType(): string;

    public function execute(array $item, Closure $traceCallback = null): ?array;
}
