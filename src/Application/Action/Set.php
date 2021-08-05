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

    public function execute(Resource $resource): void
    {
        $field = $this->getField();

        $beforeValue = $resource->get($field);

        $resultValue = $this->evaluateValue($resource);

        // skip if same value
        if ($resultValue === $beforeValue) {
            return;
        }

        $this->traceHook->onAction(ActionTrace::create($resource->getCodeOrIdentifier(), $beforeValue, $resultValue));

        $isAttribute = $resource->isAttribute($this->options->getFieldName());

        $resource->set($field, $resultValue, $isAttribute);
    }

    /**
     * @return mixed
     */
    protected function evaluateValue(Resource $resource)
    {
        if ($this->options->getValue() !== null) {
            return $this->options->getValue();
        }

        $expression = $this->options->getExpression() ?? '';

        return $this->expressionLanguage->evaluate($expression, $resource->toArray());
    }

    public function setHook(ActionTraceHook $hook): void
    {
        $this->traceHook = $hook;
    }

    private function getField(): Field
    {
        return Field::create(
            $this->options->getFieldName(),
            [
                'scope' => $this->options->getScope(),
                'locale' => $this->options->getLocale(),
            ]
        );
    }
}
