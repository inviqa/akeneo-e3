<?php

namespace App\Application;

use App\Domain\TransformerStep;
use App\Application\TransformerStep as Transformers;
use LogicException;

class TransformerStepFactory
{
    public function create(string $type, array $options): TransformerStep
    {
        // if deps needed, then clone from registry
        switch ($type) {
            case 'slug':
                return new Transformers\Slugger($options);
        }

        throw new LogicException(sprintf('No registered transformer with the name %s', $type));
    }
}
