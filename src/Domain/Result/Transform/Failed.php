<?php

namespace AkeneoE3\Domain\Result\Transform;

use AkeneoE3\Domain\Resource\BaseResource;

class Failed implements TransformResult
{
    private string $error;

    private BaseResource $resource;

    public function __construct(BaseResource $resource, string $error)
    {
        $this->error = $error;
        $this->resource = $resource;
    }

    public static function create(BaseResource $resource, string $error): self
    {
        return new self($resource, $error);
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getResource(): BaseResource
    {
        return $this->resource;
    }

    public function __toString(): string
    {
        return $this->getError();
    }
}
