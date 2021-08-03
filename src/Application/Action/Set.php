<?php

namespace AkeneoEtl\Application\Action;

use AkeneoEtl\Application\Expression\ExpressionLanguage;
use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Field;
use AkeneoEtl\Domain\Hook\ActionTrace;
use AkeneoEtl\Domain\Hook\ActionTraceHook;
use AkeneoEtl\Domain\Hook\ActionTraceHookAware;
use AkeneoEtl\Domain\Hook\EmptyHooks;
use AkeneoEtl\Domain\StandardFormat;

class Set implements Action, ActionTraceHookAware
{
    private ExpressionLanguage $expressionLanguage;
    private array $options;
    private ActionTraceHook $traceHook;

    public function __construct(ExpressionLanguage $expressionLanguage, array $options)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->options = $options;
        $this->traceHook = new EmptyHooks();

        // @todo: check that value or expression are set
    }

    public function getType(): string
    {
        return 'expression';
    }

    public function execute(array $item): ?array
    {
        $standardFormat = new StandardFormat($item);
        $field = Field::fromOptions($this->options);

        $beforeValue = $standardFormat->get($field);

        $resultValue = $this->evaluateValue($item);

        // skip if same value
        if ($resultValue === $beforeValue) {
            return null;
        }

        $this->traceHook->onAction(ActionTrace::create($item['identifier'], $beforeValue, $resultValue));

        $isAttribute = $standardFormat->isAttribute($this->options['field']);

        return $standardFormat->makeValueArray($field, $resultValue, $isAttribute);
    }

    /**
     * @return mixed
     */
    protected function evaluateValue(array $item)
    {
        if (isset($this->options['value'])) {
            return $this->options['value'];
        }

        $expression = $this->options['expression'];

        return $this->expressionLanguage->evaluate($expression, $item);
    }

    public function setHook(ActionTraceHook $hook): void
    {
        $this->traceHook = $hook;
    }
}
