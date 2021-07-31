<?php

namespace App\Application\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

require_once('src/Application/Expression/Functions/functions.php');

class ExpressionLanguage extends BaseExpressionLanguage
{
    public function __construct()
    {
        parent::__construct();

        $this->addFunction(ExpressionFunction::fromPhp('strtoupper', 'uppercase'));
        $this->addFunction(ExpressionFunction::fromPhp('strtolower', 'lowercase'));
        $this->addFunction(ExpressionFunction::fromPhp('trim', 'trim'));
        $this->addFunction(ExpressionFunction::fromPhp('\App\Application\Expression\Functions\slug', 'slug'));
    }
}
