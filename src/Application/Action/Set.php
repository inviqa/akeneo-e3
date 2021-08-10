<?php

declare(strict_types=1);

namespace AkeneoEtl\Application\Action;

use AkeneoEtl\Application\Expression\ExpressionLanguage;
use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Resource\Field;
use AkeneoEtl\Domain\Resource\FieldFactory;
use AkeneoEtl\Domain\Resource\Resource;

final class Set implements Action
{
    private Field $field;

    private SetOptions $options;

    private ExpressionLanguage $expressionLanguage;

    public function __construct(ExpressionLanguage $expressionLanguage, array $options)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->field = FieldFactory::fromOptions($options);
        $this->options = SetOptions::fromArray($options);
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
}
