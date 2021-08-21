<?php

namespace AkeneoE3\Domain\Transform\TransformResult;

use AkeneoE3\Domain\Resource\Resource;

class Failed implements TransformResult
{
    private string $error;

    private Resource $resource;

    public function __construct(Resource $resource, string $error)
    {
        $this->error = $error;
        $this->resource = $resource;
    }

    public static function create(Resource $resource, string $error): self
    {
        return new self($resource, $error);
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getResource(): Resource
    {
        return $this->resource;
    }
}
