<?php

namespace AkeneoEtl\Application\Action;

use AkeneoEtl\Application\Expression\ExpressionLanguage;
use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Hook\ActionTrace;
use AkeneoEtl\Domain\Hook\ActionTraceHook;

class Set implements Action
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

        // @todo: check that value or expression are set
    }

    public function getType(): string
    {
        return 'expression';
    }

    public function execute(array $item, ActionTraceHook $tracer = null): ?array
    {
        $beforeValue = TransformerUtils::getFieldValue(
            $item,
            $this->options['field'],
            $this->options['scope'],
            $this->options['locale']
        )['data'] ?? '';

        $resultValue = $this->resolveValue($item);

        if ($resultValue === $beforeValue) {
            return null;
        }

        if ($tracer !== null) {
            $tracer->onAction(ActionTrace::create($item['identifier'], $beforeValue, $resultValue));
        }

        return TransformerUtils::createFieldArray(
            $this->options['field'],
            $resultValue,
            $this->options['scope'],
            $this->options['locale']
        );
    }

    /**
     * @return mixed
     */
    protected function resolveValue(array $item)
    {
        if (isset($this->options['value'])) {
            return $this->options['value'];
        }

        $expression = $this->options['expression'];

        return $this->expressionLanguage->evaluate($expression, $item);
    }
}
