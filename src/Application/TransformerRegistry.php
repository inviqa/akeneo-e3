<?php

namespace AkeneoEtl\Application;

use AkeneoEtl\Domain\TransformerStep;

class TransformerRegistry
{
    private iterable $transformers;

    public function __construct(iterable $transformers)
    {
        $this->transformers = $transformers;
    }
}
