<?php

namespace AkeneoEtl\Application\Action;

use AkeneoEtl\Application\Expression\ExpressionLanguage;
use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Field;
use AkeneoEtl\Domain\Hook\ActionTrace;
use AkeneoEtl\Domain\Hook\ActionTraceHook;
use AkeneoEtl\Domain\Hook\ActionTraceHookAware;
use AkeneoEtl\Domain\Hook\EmptyHooks;
use AkeneoEtl\Domain\Resource;

class Set implements Action, ActionTraceHookAware
{
    private SetOptions $options;

    private ExpressionLanguage $expressionLanguage;

    private ActionTraceHook $traceHook;

    public function __construct(ExpressionLanguage $expressionLanguage, array $options)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->options = SetOptions::fromArray($options);
        $this->traceHook = new EmptyHooks();
    }

    public function getType(): string
    {
        return 'expression';
    }

    public function execute(array $item): ?array
    {
        $standardFormat = new Resource($item);
        $field = $this->getField();

        $beforeValue = $standardFormat->get($field);

        $resultValue = $this->evaluateValue($item);

        // skip if same value
        if ($resultValue === $beforeValue) {
            return null;
        }

        $this->traceHook->onAction(ActionTrace::create($item['identifier'], $beforeValue, $resultValue));

        $isAttribute = $standardFormat->isAttribute($this->options->getFieldName());

        return $standardFormat->makeValueArray($field, $resultValue, $isAttribute);
    }

    /**
     * @return mixed
     */
    protected function evaluateValue(array $item)
    {
        if ($this->options->getValue() !== null) {
            return $this->options->getValue();
        }

        $expression = $this->options->getExpression() ?? '';

        return $this->expressionLanguage->evaluate($expression, $item);
    }

    public function setHook(ActionTraceHook $hook): void
    {
        $this->traceHook = $hook;
    }

    private function getField(): \AkeneoEtl\Domain\Field
    {
        $field = Field::create(
            $this->options->getFieldName(),
            [
                'scope' => $this->options->getScope(),
                'locale' => $this->options->getLocale(),
            ]
        );

        return $field;
    }
}
