<?php

namespace AkeneoE3\Domain\Result\Transform;

use AkeneoE3\Domain\Resource\BaseResource;

class Transformed implements TransformResult
{
    private BaseResource $resource;

    public function __construct(BaseResource $resource)
    {
        $this->resource = $resource;
    }

    public static function create(BaseResource $resource): self
    {
        return new self($resource);
    }

    public function getResource(): BaseResource
    {
        return $this->resource;
    }

    public function __toString(): string
    {
        return 'transformed';
    }
}
