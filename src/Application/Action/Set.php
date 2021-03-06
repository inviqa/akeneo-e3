<?php

declare(strict_types=1);

namespace AkeneoE3\Application\Action;

use AkeneoE3\Application\Expression\ActionState;
use AkeneoE3\Application\Expression\ExpressionEvaluator;
use AkeneoE3\Domain\Action;
use AkeneoE3\Domain\Resource\Field;
use AkeneoE3\Domain\Resource\FieldFactory;
use AkeneoE3\Domain\Resource\TransformableResource;

final class Set implements Action
{
    private Field $field;

    private SetOptions $options;

    private ExpressionEvaluator $expressionLanguage;

    public function __construct(ExpressionEvaluator $expressionLanguage, array $options)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->field = FieldFactory::fromOptions($options);
        $this->options = SetOptions::fromArray($options);
    }

    public function execute(TransformableResource $resource): void
    {
        $resultValue = $this->evaluateValue($resource);

        $resource->set($this->field, $resultValue);
    }

    /**
     * @return mixed
     */
    protected function evaluateValue(TransformableResource $resource)
    {
        if ($this->options->getExpression() === null) {
            return $this->options->getValue();
        }

        ActionState::setResourceAndField($resource, $this->field);
        $expression = $this->options->getExpression() ?? '';

        return $this->expressionLanguage->evaluate($expression, $resource->toArray());
    }
}
