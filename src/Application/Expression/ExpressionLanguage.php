<?php

declare(strict_types=1);

namespace AkeneoEtl\Application\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

final class ExpressionLanguage extends BaseExpressionLanguage
{
    public function __construct()
    {
        parent::__construct(null, [
            new FunctionProvider(),
        ]);
    }
}
