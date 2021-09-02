<?php

namespace AkeneoE3\Domain\Result\Write;

use AkeneoE3\Domain\Resource\WritableResource;
use AkeneoE3\Domain\Resource\TransformableResource;
use AkeneoE3\Domain\Result\Transform\TransformResult;

class TransformFailed implements WriteResult
{
    private WritableResource $resource;

    private TransformResult $transformResult;

    public function __construct(WritableResource $resource, TransformResult $transformResult)
    {
        $this->resource = $resource;
        $this->transformResult = $transformResult;
    }

    public static function create(WritableResource $resource, TransformResult $transformResult): self
    {
        return new self($resource, $transformResult);
    }

    public function getResource(): WritableResource
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
