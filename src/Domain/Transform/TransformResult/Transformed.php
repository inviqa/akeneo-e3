<?php

namespace AkeneoE3\Domain\Transform\TransformResult;

use AkeneoE3\Domain\Resource\Resource;

class Transformed implements TransformResult
{
    private Resource $resource;

    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    public static function create(Resource $resource): self
    {
        return new self($resource);
    }

    public function getResource(): Resource
    {
        return $this->resource;
    }

    public function __toString(): string
    {
        return 'transformed';
    }
}
