<?php

namespace AkeneoE3\Infrastructure\Loader;

use AkeneoE3\Domain\Loader;
use AkeneoE3\Domain\Resource\Resource;

class ApiLoader implements Loader
{
    private LoadConnector $connector;

    public function __construct(LoadConnector $connector)
    {
        $this->connector = $connector;
    }

    public function load(Resource $resource): iterable
    {
        $result = $this->connector->load($resource);

        return [$result];
    }

    public function finish(): array
    {
        return [];
    }
}
