<?php

declare(strict_types=1);

namespace AkeneoEtl\Application\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

require_once('src/Application/Expression/Functions/functions.php');

final class ExpressionLanguage extends BaseExpressionLanguage
{
    public function __construct()
    {
        parent::__construct();

        $namespace = '\AkeneoEtl\Application\Expression\Functions\\';

        $functionMap = [
            // 'trim' => 'trim', // can register simple php functions
            $namespace.'trim'  => 'trim',
            $namespace.'slug'  => 'slug',
            $namespace.'lowercase'  => 'lowercase',
            $namespace.'uppercase'  => 'uppercase',
            $namespace.'camelcase'  => 'camelcase',
            $namespace.'snakecase'  => 'snakecase',
            $namespace.'value'  => 'value',
        ];

        foreach ($functionMap as $phpFunction => $expressionFunction) {
            $this->addFunction(ExpressionFunction::fromPhp($phpFunction, $expressionFunction));
        }
    }
}
