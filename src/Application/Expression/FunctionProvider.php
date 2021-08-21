<?php

declare(strict_types=1);

namespace AkeneoE3\Application\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

require_once('src/Application/Expression/Functions/functions.php');

final class FunctionProvider implements ExpressionFunctionProviderInterface
{
    public const EXPRESSION_FUNCTIONS_NAMESPACE = '\AkeneoE3\Application\Expression\Functions\\';

    /**
     * @return array|ExpressionFunction[]
     */
    public function getFunctions(): array
    {
        $namespace = self::EXPRESSION_FUNCTIONS_NAMESPACE;

        $functionMap = [
            // 'trim' => 'trim', // can register simple php functions
            'value'          => 'value',
            'hasAttribute'   => 'hasAttribute',
            'hasProperty'    => 'hasProperty',
            'removeHtmlTags' => 'removeHtmlTags',
            'trim'           => 'trim',
            'slug'           => 'slug',
            'replace'        => 'replace',
            'lowerCase'      => 'lowerCase',
            'upperCase'      => 'upperCase',
            'camelCase'      => 'camelCase',
            'snakeCase'      => 'snakeCase',
        ];

        foreach ($functionMap as $phpFunction => $expressionFunction) {
            $functions[] = ExpressionFunction::fromPhp($namespace.$phpFunction, $expressionFunction);
        }

        return $functions;
    }
}
