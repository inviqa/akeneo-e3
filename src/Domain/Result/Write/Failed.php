<?php

namespace AkeneoE3\Domain\Result\Write;

use AkeneoE3\Domain\Resource\ImmutableResource;

class Failed implements WriteResult
{
    private string $error;

    private ImmutableResource $resource;

    public function __construct(ImmutableResource $resource, string $error)
    {
        $this->error = $error;
        $this->resource = $resource;
    }

    public static function create(ImmutableResource $resource, string $error): self
    {
        return new self($resource, $error);
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getResource(): ImmutableResource
    {
        return $this->resource;
    }


    public function __toString(): string
    {
        return $this->getError();
    }
}
