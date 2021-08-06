<?php

namespace AkeneoEtl\Tests\Acceptance\bootstrap;

use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Resource;

class InMemoryLoader implements Loader
{
    private \AkeneoEtl\Domain\Resource $originalResource;

    private array $result = [];

    public function __construct(Resource $originalResource)
    {
        $this->originalResource = $originalResource;
    }

    public function queue(Resource $resource): void
    {
        $this->result = array_merge(
            $this->originalResource->toArray(),
            $resource->toArray()
        );
    }

    public function load(): void
    {
        // nothing
    }

    public function getResult(): array
    {
        return $this->result;
    }
}
