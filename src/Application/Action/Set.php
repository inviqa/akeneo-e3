<?php

namespace AkeneoEtl\Application\Action;

use AkeneoEtl\Application\Expression\ExpressionLanguage;
use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Hook\ActionTrace;
use AkeneoEtl\Domain\Hook\ActionTraceHook;

class Set implements Action
{
    private ExpressionLanguage $expressionLanguage;
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
        $standardFormat = new StandardFormat($item);
        $field = Field::fromOptions($this->options);

        $beforeValue = $standardFormat->get($field, );

        $resultValue = $this->evaluateValue($item);

        // skip if same value
        if ($resultValue === $beforeValue) {
            return null;
        }

        if ($tracer !== null) {
            $tracer->onAction(ActionTrace::create($item['identifier'], $beforeValue, $resultValue));
        }

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
}
