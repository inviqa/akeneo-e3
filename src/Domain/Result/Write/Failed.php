<?php

namespace AkeneoE3\Domain\Result\Write;

use AkeneoE3\Domain\Resource\WritableResource;

class Failed implements WriteResult
{
    private string $error;

    private WritableResource $resource;

    public function __construct(WritableResource $resource, string $error)
    {
        $this->error = $error;
        $this->resource = $resource;
    }

    public static function create(WritableResource $resource, string $error): self
    {
        return new self($resource, $error);
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getResource(): WritableResource
    {
        return $this->resource;
    }


    public function __toString(): string
    {
        return $this->getError();
    }
}
