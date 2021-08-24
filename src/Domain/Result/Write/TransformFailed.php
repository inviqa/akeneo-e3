<?php

namespace AkeneoE3\Domain\Result\Write;

use AkeneoE3\Domain\Resource\ImmutableResource;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Result\Transform\TransformResult;

class TransformFailed implements WriteResult
{
    private ImmutableResource $resource;

    private TransformResult $transformResult;

    public function __construct(ImmutableResource $resource, TransformResult $transformResult)
    {
        $this->resource = $resource;
        $this->transformResult = $transformResult;
    }

    public static function create(ImmutableResource $resource, TransformResult $transformResult): self
    {
        return new self($resource, $transformResult);
    }

    public function getResource(): ImmutableResource
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
