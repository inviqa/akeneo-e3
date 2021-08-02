<?php

namespace AkeneoEtl\Application;

use AkeneoEtl\Domain\TransformerStep;

class TransformerRegistry
{
    /**
     * @var TransformerStep[]|iterable
     */
    private iterable $transformers;

    /**
     * @param TransformerStep[]|iterable $transformers
     */
    public function __construct(iterable $transformers)
    {
        $this->transformers = $transformers;
    }
}
