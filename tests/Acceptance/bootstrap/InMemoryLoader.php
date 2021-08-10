<?php

namespace AkeneoEtl\Tests\Acceptance\bootstrap;

use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Resource\Resource;

class InMemoryLoader implements Loader
{
    private \AkeneoEtl\Domain\Resource\Resource $originalResource;

    private \AkeneoEtl\Domain\Resource\Resource $result;

    public function __construct(Resource $originalResource)
    {
        $this->originalResource = $originalResource;
    }

    public function load(Resource $resource): void
    {
        $this->result = $this->originalResource->merge($resource);
    }

    public function finish(): void
    {
        // nothing
    }

    public function getResult(): \AkeneoEtl\Domain\Resource\Resource
    {
        return $this->result;
    }
}
