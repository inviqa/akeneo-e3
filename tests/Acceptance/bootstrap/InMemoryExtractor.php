<?php

namespace AkeneoE3\Tests\Acceptance\bootstrap;

use AkeneoE3\Domain\Repository\Query;
use AkeneoE3\Domain\Repository\ReadRepository;
use AkeneoE3\Domain\Resource\Resource;

class InMemoryExtractor implements ReadRepository
{
    private Resource $resource;

    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function count(Query $query): int
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function read(Query $query): iterable
    {
        yield $this->resource;
    }
}
