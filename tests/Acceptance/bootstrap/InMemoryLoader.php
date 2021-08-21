<?php

namespace AkeneoEtl\Tests\Acceptance\bootstrap;

use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Profile\EtlProfile;
use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Domain\Resource\AuditableResource;
use LogicException;
use Webmozart\Assert\Assert;

class InMemoryLoader implements Loader
{
    private Resource $originalResource;

    private ?AuditableResource $result;

    private string $uploadMode;

    public function __construct(Resource $originalResource, string $uploadMode = EtlProfile::MODE_UPDATE)
    {
        $this->originalResource = $originalResource;
        $this->uploadMode = $uploadMode;
        $this->result = null;
    }

    public function load(Resource $resource): array
    {
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

    public function getResult(): Resource
    {
        Assert::notNull($this->result, 'Transformation result is not defined.');

        if ($this->uploadMode === EtlProfile::MODE_UPDATE) {
            return $this->result->changes();
        }

        return $this->result;
    }

    public function isResultEmpty(): bool
    {
        return $this->result === null;
    }
}
