<?php

namespace AkeneoEtl\Tests\Shared;

use AkeneoEtl\Application\Expression\FunctionProvider;
use AkeneoEtl\Application\Expression\StateHolder;
use AkeneoEtl\Domain\Resource\Resource;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlockFactory;
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

        $factory = DocBlockFactory::createInstance();

        StateHolder::$resource = Resource::fromArray([
            'identifier' => 'the-ziggy',
            'values' => [
                'name' =>[['scope' => null, 'locale' => 'en_GB', 'data' => 'Ziggy']],
                'description' =>[['scope' => 'web', 'locale' => 'en_GB', 'data' => 'Ziggy The Hydra']],
            ]
        ], 'product');

        foreach ($this->functionProvider->getFunctions() as $function) {
            $name = $function->getName();

            $refFunction = new ReflectionFunction(FunctionProvider::EXPRESSION_FUNCTIONS_NAMESPACE.$name);

            $comment = $refFunction->getDocComment();

            if ($comment === false) {
                continue;
            }

            $docblock = $factory->create($comment);

            $parameters = [];
            $examples = [];

            $tags = $docblock->getTags();

            foreach ($tags as $tag) {
                if ($tag instanceof Param) {
                    $parameters[$tag->getVariableName()] = [
                        'name' => $tag->getVariableName(),
                        'type' => $tag->getType() ?? '',
                        'description' => $tag->getDescription() ?? '',
                    ];
                }

                if ($tag instanceof Generic && $tag->getName() === 'meta-arguments') {
                    $content = $tag->getDescription();

                    $arguments = $this->parseArguments($content);

                    $invokeResult = $refFunction->invokeArgs($arguments);

                    $examples[] = [
                        'arguments' => $content,
                        'result' => $invokeResult,
                    ];
                }
            }

            $result[$name] = [
                'summary' => $docblock->getSummary(),
                'description' => (string)$docblock->getDescription(),
                'parameters' => $parameters,
                'examples' => $examples,
            ];
        }

        return $result;
    }

    protected function parseArguments(string $content): array
    {
        $rawArguments = str_getcsv($content);

        return array_map(function ($item) {
            if (trim($item) === 'null') {
                return null;
            }

            $matches = [];
            if (preg_match('/^\["(.*)"]$/', trim($item), $matches) === 1) {
                return [$matches[1]];
            }

            return $item;
        }, $rawArguments);
    }
}
