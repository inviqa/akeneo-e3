<?php

namespace AkeneoEtl\Tests\Shared;

use AkeneoEtl\Application\Expression\FunctionProvider;
use ReflectionFunction;

require_once('src/Application/Expression/Functions/functions.php');

class FunctionDocumentor
{
    private FunctionProvider $functionProvider;

    public function __construct(FunctionProvider $functionProvider)
    {
        $this->functionProvider = $functionProvider;
    }

    public function getFunctions(): array
    {
        $result = [];

        foreach ($this->functionProvider->getFunctions() as $function) {
            $name = $function->getName();

            $refFunction = new ReflectionFunction(FunctionProvider::EXPRESSION_FUNCTIONS_NAMESPACE.$name);

            $docComment = $refFunction->getDocComment();
            $docComment = trim(str_replace(['/**', '*/', ' * ', ' *'], [], $docComment));

            $result[$name]['description'] = $docComment;
        }

        return $result;
    }
}
