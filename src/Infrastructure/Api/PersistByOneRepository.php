<?php

namespace AkeneoE3\Infrastructure\Api;

use AkeneoE3\Domain\Repository\PersistRepository as BasePersistRepository;
use AkeneoE3\Domain\Resource\WritableResource;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourceRepository;

class PersistByOneRepository implements BasePersistRepository
{
    private WriteResourceRepository $connector;

    public function __construct(WriteResourceRepository $connector)
    {
        $this->connector = $connector;
    }

    public function persist(WritableResource $resource, bool $patch): iterable
    {
        $result = $this->connector->write($resource, $patch);

        return [$result];
    }

    public function flush(bool $patch): iterable
    {
        return [];
    }
}
