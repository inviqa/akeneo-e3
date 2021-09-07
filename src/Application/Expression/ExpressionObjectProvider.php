<?php

namespace AkeneoE3\Application\Expression;

interface ExpressionObjectProvider
{
    public function get(): ExpressionObject;
}
