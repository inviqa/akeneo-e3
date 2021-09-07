<?php

namespace AkeneoE3\Application\Expression;

interface ExpressionEvaluator
{
    /**
     * @return mixed|null
     */
    public function evaluate(string $expression, array $values = []);
}
