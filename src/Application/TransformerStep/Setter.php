<?php

namespace AkeneoEtl\Application\TransformerStep;

use AkeneoEtl\Domain\TransformerStep;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Setter implements TransformerStep
{
    private ExpressionLanguage $expressionLanguage;

    /**
     * @var array
     */
    private array $options;

    public function __construct(ExpressionLanguage $expressionLanguage, array $options)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->options = $options;
    }

    public function getType(): string
    {
        return 'expression';
    }

    public function transform(array $item): ?array
    {
        $expression = $this->options['value'];

        $finalValue = $this->expressionLanguage->evaluate($expression, $item);

        return TransformerUtils::createFieldArray(
            $this->options['field'],
            $finalValue,
            $this->options['scope'],
            $this->options['locale']
        );
    }
}
