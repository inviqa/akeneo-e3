<?php

namespace AkeneoE3\Application\Action;

use AkeneoE3\Application\Expression\ActionState;
use AkeneoE3\Application\Expression\ExpressionEvaluator;
use AkeneoE3\Domain\Action;
use AkeneoE3\Domain\Resource\TransformableResource;

class Duplicate implements Action
{
    private DuplicateOptions $options;

    private ExpressionEvaluator $expressionEvaluator;

    public function __construct(ExpressionEvaluator $expressionEvaluator, array $options)
    {
        $this->expressionEvaluator = $expressionEvaluator;
        $this->options = DuplicateOptions::fromArray($options);
    }

    public function execute(TransformableResource $resource): void
    {
        $includeFieldNames = $this->options->getIncludeFieldNames();
        if ($this->options->getIncludeFieldNamesExpression() !== '') {
            $includeFieldNames = $this->expressionEvaluator->evaluate($this->options->getIncludeFieldNamesExpression(), $resource->toArray(true));
        }

        $excludeFieldNames = $this->options->getExcludeFieldNames();
        if ($this->options->getExcludeFieldNamesExpression() !== '') {
            $excludeFieldNames = $this->expressionEvaluator->evaluate($this->options->getExcludeFieldNamesExpression(), $resource->toArray(true));
        }

        $resource->duplicate($includeFieldNames, $excludeFieldNames);
    }
}
