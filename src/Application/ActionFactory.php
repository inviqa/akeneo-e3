<?php

namespace AkeneoEtl\Application;

use AkeneoEtl\Domain\Action;
use AkeneoEtl\Application\Action as Actions;
use LogicException;
use AkeneoEtl\Application\Expression\ExpressionLanguage;

class ActionFactory
{
    public function create(string $type, array $options): Action
    {
        // if deps needed, then clone from registry
        switch ($type) {
            case 'set':
                return new Actions\Set(new ExpressionLanguage(), $options);
            case 'copy-all':
                return new Actions\CopyAll($options);
        }

        throw new LogicException(sprintf('No registered action with the name %s', $type));
    }
}
