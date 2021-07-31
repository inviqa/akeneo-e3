<?php

namespace AkeneoEtl\Domain;

class EtlTransformProfile
{
    /**
     * @var iterable|TransformerStep[]
     */
    public iterable $transformerSteps;

    public function __construct(iterable $transformers)
    {
        $this->transformerSteps = $transformers;
    }
}
