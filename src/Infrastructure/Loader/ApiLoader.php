<?php

namespace AkeneoE3\Infrastructure\Loader;

use AkeneoE3\Domain\Repository\WriteRepository;
use AkeneoE3\Domain\Resource\Resource;

class ApiLoader implements WriteRepository
{
    private LoadConnector $connector;

    public function __construct(LoadConnector $connector)
    {
        $this->connector = $connector;
    }

    public function persist(Resource $resource): iterable
    {
        $result = $this->connector->load($resource);

        return [$result];
    }

    public function flush(): iterable
    {
        return [];
    }
}
