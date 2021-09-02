<?php

namespace AkeneoE3\Tests\Acceptance\bootstrap;

use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Repository\PersistRepository;
use AkeneoE3\Domain\Resource\WritableResource;
use AkeneoE3\Domain\Resource\TransformableResource;
use AkeneoE3\Domain\Resource\Resource;
use LogicException;
use Webmozart\Assert\Assert;

class InMemoryLoader implements PersistRepository
{
    private ?WritableResource $result;

    public function __construct()
    {
        $this->result = null;
    }

    public function persist(WritableResource $resource): iterable
    {
        $this->result = $resource;

        return [];
    }

    public function flush(): iterable
    {
        return [];
    }

    public function getResult(): WritableResource
    {
        Assert::notNull($this->result, 'Transformation result is not defined.');

        return $this->result;
    }

    public function isResultEmpty(): bool
    {
        return $this->result === null;
    }
}
