<?php

namespace AkeneoE3\Infrastructure\Loader;

use AkeneoE3\Domain\Repository\WriteRepository;
use AkeneoE3\Domain\Resource\Resource;

class ApiLoader implements WriteRepository
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
