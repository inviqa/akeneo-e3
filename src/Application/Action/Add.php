<?php

declare(strict_types=1);

namespace AkeneoE3\Application\Action;

use AkeneoE3\Application\Expression\ActionState;
use AkeneoE3\Application\Expression\ExpressionEvaluator;
use AkeneoE3\Domain\Action;
use AkeneoE3\Domain\Exception\TransformException;
use AkeneoE3\Domain\Resource\Field;
use AkeneoE3\Domain\Resource\FieldFactory;
use AkeneoE3\Domain\Resource\TransformableResource;

final class Add implements Action
{
    private Field $field;

    private AddRemoveOptions $options;

    private ExpressionEvaluator $expressionLanguage;

    public function __construct(ExpressionEvaluator $expressionLanguage, array $options)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->field = FieldFactory::fromOptions($options);
        $this->options = AddRemoveOptions::fromArray($options);
    }

    public function execute(TransformableResource $resource): void
    {
        $resultValue = $this->evaluateValue($resource);

        $resource->addTo($this->field, $resultValue);
    }

    protected function evaluateValue(TransformableResource $resource): array
    {
        if ($this->options->getExpression() === null) {
            return $this->options->getItems();
        }

        ActionState::setResourceAndField($resource, $this->field);

        $expression = $this->options->getExpression();
        $result = $this->expressionLanguage->evaluate($expression, $resource->toArray());

        if (is_array($result) === false) {
            throw new TransformException(sprintf('Expected result type of the expression %s is array', $expression), true);
        }

        return $result;
    }
}
