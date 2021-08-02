<?php

namespace AkeneoEtl\Domain;

use Closure;

interface TransformerStep
{
    public function getType(): string;

    public function transform(array $item, Closure $traceCallback = null): ?array;
}
