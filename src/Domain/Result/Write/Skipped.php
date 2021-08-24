<?php

namespace AkeneoE3\Domain\Result\Write;

use AkeneoE3\Domain\Resource\ImmutableResource;

class Skipped implements WriteResult
{
    private ImmutableResource $resource;

    public function __construct(ImmutableResource $resource)
    {
        $this->resource = $resource;
    }

    public static function create(ImmutableResource $resource): self
    {
        return new self($resource);
    }

    public function getResource(): ImmutableResource
    {
        return $this->resource;
    }

    public function __toString(): string
    {
        return 'skipped';
    }
}
