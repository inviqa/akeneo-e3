<?php

namespace AkeneoEtl\Domain\Load\LoadResult;

use AkeneoEtl\Domain\Resource\Resource;

class Loaded implements LoadResult
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
