<?php

declare(strict_types=1);

namespace AkeneoE3\Application\Action;

use AkeneoE3\Application\Expression\ActionState;
use AkeneoE3\Application\Expression\ExpressionLanguage;
use AkeneoE3\Domain\Action;
use AkeneoE3\Domain\Resource\Field;
use AkeneoE3\Domain\Resource\FieldFactory;
use AkeneoE3\Domain\Resource\Resource;

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

        ActionState::setResourceAndField($resource, $this->field);
        $expression = $this->options->getExpression() ?? '';

        return $this->expressionLanguage->evaluate($expression, $resource->toArray(true));
    }
}
