<?php

namespace AkeneoEtl\Tests\Acceptance\bootstrap;

use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Domain\Resource\AuditableResource;
use LogicException;
use Webmozart\Assert\Assert;

class InMemoryLoader implements Loader
{
    private Resource $originalResource;

    private ?\AkeneoEtl\Domain\Resource\Resource $result;

    private bool $isMergeMode;

    public function __construct(Resource $originalResource, bool $mergeResource = true)
    {
        $this->originalResource = $originalResource;
        $this->isMergeMode = $mergeResource;
        $this->result = null;
    }

    public function load(Resource $resource): array
    {
//        if ($this->isMergeMode === false) {
//            $this->result = $resource;
//
//            return [];
//        }
        if (!$resource instanceof AuditableResource) {
            throw new LogicException('Resource must be auditable for merge');
        }

        $this->result = $resource;

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

    private function merge(Resource $resource)
    {
        if (!$resource instanceof AuditableResource) {
            throw new LogicException('Resource must be auditable for merge');
        }

        foreach ($resource->changes()->fields() as $field) {
        }

        return $this->originalResource->merge($resource);
    }
}
