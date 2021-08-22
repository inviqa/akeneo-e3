<?php

namespace AkeneoE3\Domain\Load\LoadResult;

use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Transform\TransformResult\TransformResult;

class TransformFailed implements LoadResult
{
    private Resource $resource;

    private TransformResult $transformResult;

    public function __construct(Resource $resource, TransformResult $transformResult)
    {
        $this->resource = $resource;
        $this->transformResult = $transformResult;
    }

    public static function create(Resource $resource, TransformResult $transformResult): self
    {
        return new self($resource, $transformResult);
    }

    public function getResource(): Resource
    {
        return $this->resource;
    }

    public function getTransformResult(): TransformResult
    {
        return $this->transformResult;
    }

    public function __toString(): string
    {
        return $this->transformResult;
    }
}
