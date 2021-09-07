<?php

namespace AkeneoE3\Tests\Acceptance\bootstrap;

use AkeneoE3\Application\Expression\ExpressionObject;

class EmptyExpressionObject implements ExpressionObject
{
    public function getName(): string
    {
        return 'empty';
    }
}
