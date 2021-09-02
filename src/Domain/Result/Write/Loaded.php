<?php

namespace AkeneoE3\Domain\Result\Write;

use AkeneoE3\Domain\Resource\WritableResource;
use AkeneoE3\Domain\Resource\TransformableResource;

class Loaded implements WriteResult
{
    private WritableResource $resource;

    public function __construct(WritableResource $resource)
    {
        $this->resource = $resource;
    }

    public static function create(WritableResource $resource): self
    {
        return new self($resource);
    }

    public function getResource(): WritableResource
    {
        return $this->resource;
    }

    public function __toString(): string
    {
        return 'ok';
    }
}
