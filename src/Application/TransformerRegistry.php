<?php

namespace App\Application;

use App\Domain\TransformerStep;

class TransformerRegistry
{
    private iterable $transformers;

    public function __construct(iterable $transformers)
    {
        $this->transformers = $transformers;
    }
}
