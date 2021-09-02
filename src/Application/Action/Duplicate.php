<?php

namespace AkeneoE3\Application\Action;

use AkeneoE3\Domain\Action;
use AkeneoE3\Domain\Resource\TransformableResource;

class Duplicate implements Action
{
    private array $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function execute(TransformableResource $resource): void
    {
        $resource->duplicate([], []);
    }
}
