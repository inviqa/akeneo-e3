<?php

declare(strict_types=1);

namespace AkeneoEtl\Application\Action;

use AkeneoEtl\Application\Expression\ExpressionLanguage;
use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Field;
use AkeneoEtl\Domain\FieldFactory;
use AkeneoEtl\Domain\Hook\ActionTrace;
use AkeneoEtl\Domain\Hook\ActionTraceHook;
use AkeneoEtl\Domain\Hook\ActionTraceHookAware;
use AkeneoEtl\Domain\Hook\EmptyHooks;
use AkeneoEtl\Domain\Resource;

final class Set implements Action, ActionTraceHookAware
{
    private Field $field;

    private SetOptions $options;

    private ExpressionLanguage $expressionLanguage;

    private ActionTraceHook $traceHook;

    public function __construct(ExpressionLanguage $expressionLanguage, array $options)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->field = FieldFactory::fromOptions($options);
        $this->options = SetOptions::fromArray($options);
        $this->traceHook = new EmptyHooks();
    }

    public function getType(): string
    {
        return 'expression';
    }

    public function execute(Resource $resource): void
    {
        $resultValue = $this->evaluateValue($resource);

        $resource->set($this->field, $resultValue);
    }

    /**
     * @return mixed
     */
    protected function evaluateValue(Resource $resource)
    {
        if ($this->options->getExpression() === null) {
            return $this->options->getValue();
        }

        $expression = $this->options->getExpression() ?? '';

        return $this->expressionLanguage->evaluate($expression, $resource->toArray());
    }

    public function setHook(ActionTraceHook $hook): void
    {
        $this->traceHook = $hook;
    }
}
