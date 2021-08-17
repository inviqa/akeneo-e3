<?php

declare(strict_types=1);

namespace AkeneoEtl\Application\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

require_once('src/Application/Expression/Functions/functions.php');

final class FunctionProvider implements ExpressionFunctionProviderInterface
{
    const EXPRESSION_FUNCTIONS_NAMESPACE = '\AkeneoEtl\Application\Expression\Functions\\';

    /**
     * @return array|ExpressionFunction[]
     */
    public function getFunctions(): array
    {
        $namespace = self::EXPRESSION_FUNCTIONS_NAMESPACE;

        $functionMap = [
            // 'trim' => 'trim', // can register simple php functions
            'slug'           => 'slug',
            'trim'           => 'trim',
            'lowerCase'      => 'lowerCase',
            'upperCase'      => 'upperCase',
            'camelCase'      => 'camelCase',
            'snakeCase'      => 'snakeCase',
            'value'          => 'value',
            'removeHtmlTags' => 'removeHtmlTags',
        ];

        foreach ($functionMap as $phpFunction => $expressionFunction) {
            $functions[] = ExpressionFunction::fromPhp($namespace.$phpFunction, $expressionFunction);
        }

        return $functions;
    }
}
