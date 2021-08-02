<?php

namespace AkeneoEtl\Application;

use AkeneoEtl\Domain\TransformerStep;
use AkeneoEtl\Application\TransformerStep as Transformers;
use LogicException;
use AkeneoEtl\Application\Expression\ExpressionLanguage;

class TransformerStepFactory
{
    public function create(string $type, array $options): TransformerStep
    {
        // if deps needed, then clone from registry
        switch ($type) {
            case 'set':
                return new Transformers\Set(new ExpressionLanguage(), $options);
            case 'copy-all':
                return new Transformers\CopyAll($options);
        }

        throw new LogicException(sprintf('No registered transformer with the name %s', $type));
    }
}
