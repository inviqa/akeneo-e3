<?php

declare(strict_types=1);

namespace AkeneoEtl\Application\Action;

use AkeneoEtl\Application\Expression\StateHolder;
use AkeneoEtl\Application\Expression\ExpressionLanguage;
use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Exception\TransformException;
use AkeneoEtl\Domain\Resource\Field;
use AkeneoEtl\Domain\Resource\FieldFactory;
use AkeneoEtl\Domain\Resource\Resource;

final class Add implements Action
{
    private Field $field;

    private AddRemoveOptions $options;

    private ExpressionLanguage $expressionLanguage;

    public function __construct(ExpressionLanguage $expressionLanguage, array $options)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->field = FieldFactory::fromOptions($options);
        $this->options = AddRemoveOptions::fromArray($options);
    }

    public function execute(Resource $resource): void
    {
        $resultValue = $this->evaluateValue($resource);

        $resource->addTo($this->field, $resultValue);
    }

    protected function evaluateValue(Resource $resource): array
    {
        if ($this->options->getExpression() === null) {
            return $this->options->getItems();
        }

        $expression = $this->options->getExpression() ?? '';

        StateHolder::$resource = $resource;
        StateHolder::$field = $this->field;

        $result = $this->expressionLanguage->evaluate(
            $expression,
            $resource->toArray()
        );

        if (is_array($result) === false) {
            throw new TransformException(sprintf('Expected result type of the expression %s is array', $expression), true);
        }

        return $result;
    }
}
