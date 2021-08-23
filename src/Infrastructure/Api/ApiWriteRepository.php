<?php

namespace AkeneoE3\Infrastructure\Api;

use AkeneoE3\Domain\Repository\WriteRepository;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourceRepository;

class ApiWriteRepository implements WriteRepository
{
    private WriteResourceRepository $connector;

    public function __construct(WriteResourceRepository $connector)
    {
        $this->connector = $connector;
    }

    public function persist(Resource $resource, bool $patch): iterable
    {
        $result = $this->connector->write($resource, $patch);

        return [$result];
    }

    public function flush(bool $patch): iterable
    {
        return [];
    }
}
