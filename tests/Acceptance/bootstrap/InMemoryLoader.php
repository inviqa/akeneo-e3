<?php

namespace AkeneoEtl\Tests\Acceptance\bootstrap;

use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Resource;

class InMemoryLoader implements Loader
{
    private \AkeneoEtl\Domain\Resource $originalResource;

    private \AkeneoEtl\Domain\Resource $result;

    public function __construct(Resource $originalResource)
    {
        $this->originalResource = $originalResource;
    }

    public function queue(Resource $resource): void
    {
        $this->result = $this->originalResource->merge($resource);
    }

    public function load(): void
    {
        // nothing
    }

    public function getResult(): \AkeneoEtl\Domain\Resource
    {
        return $this->result;
    }
}
