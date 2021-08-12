<?php

namespace AkeneoEtl\Domain\Transform\TransformResult;

use AkeneoEtl\Domain\Resource\Resource;

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
}
