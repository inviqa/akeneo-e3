<?php

declare(strict_types=1);

namespace AkeneoE3\Application\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

final class E3ExpressionEvaluator implements ExpressionEvaluator
{
    private BaseExpressionLanguage $expressionLanguage;

    private ExpressionObject $expressionObject;

    public function __construct(ExpressionObject $expressionObject)
    {
        $this->expressionLanguage = new BaseExpressionLanguage(
            null,
            [new FunctionProvider()]
        );

        $this->expressionObject = $expressionObject;
    }

    /**
     * @return mixed|null
     */
    public function evaluate(string $expression, array $values = [])
    {
        $expressionValues = array_merge($values, [$this->expressionObject->getName() => $this->expressionObject]);

        return $this->expressionLanguage->evaluate($expression, $expressionValues);
    }
}
