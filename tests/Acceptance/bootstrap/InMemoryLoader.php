<?php

namespace AkeneoEtl\Tests\Acceptance\bootstrap;

use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Resource\Resource;
use Webmozart\Assert\Assert;

class InMemoryLoader implements Loader
{
    private \AkeneoEtl\Domain\Resource\Resource $originalResource;

    private ?\AkeneoEtl\Domain\Resource\Resource $result;

    private bool $mergeResource;

    public function __construct(Resource $originalResource, bool $mergeResource = true)
    {
        $this->originalResource = $originalResource;
        $this->mergeResource = $mergeResource;
        $this->result = null;
    }

    public function load(Resource $resource): array
    {
        if ($this->mergeResource === false) {
            $this->result = $resource;

            return [];
        }

        $this->result = $this->originalResource->merge($resource);

        return [];
    }

    public function finish(): array
    {
        return [];
    }

    public function getResult(): \AkeneoEtl\Domain\Resource\Resource
    {
        Assert::notNull($this->result, 'Transformation result is not defined.');

        return $this->result;
    }

    public function isResultEmpty(): bool
    {
        return $this->result === null;
    }
}
