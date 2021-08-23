<?php

namespace AkeneoE3\Tests\Acceptance\bootstrap;

use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Repository\WriteRepository;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\AuditableResource;
use LogicException;
use Webmozart\Assert\Assert;

class InMemoryLoader implements WriteRepository
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

    public function persist(Resource $resource): iterable
    {
        if (!$resource instanceof AuditableResource) {
            throw new LogicException('Resource must be auditable for merge');
        }

        $this->result = $resource;

        return [];
    }

    public function flush(): iterable
    {
        return [];
    }

    public function getResult(): Resource
    {
        Assert::notNull($this->result, 'Transformation result is not defined.');

        return $this->result;
    }

    public function isResultEmpty(): bool
    {
        return $this->result === null;
    }
}
