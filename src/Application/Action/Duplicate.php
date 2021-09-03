<?php

namespace AkeneoE3\Application\Action;

use AkeneoE3\Domain\Action;
use AkeneoE3\Domain\Resource\TransformableResource;

class Duplicate implements Action
{
    private DuplicateOptions $options;

    public function __construct(array $options)
    {
        $this->options = DuplicateOptions::fromArray($options);
    }

    public function execute(TransformableResource $resource): void
    {
        $resource->duplicate(
            $this->options->getIncludeFieldNames(),
            $this->options->getExcludeFieldNames()
        );
    }
}
