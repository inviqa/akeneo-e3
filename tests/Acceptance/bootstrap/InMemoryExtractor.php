<?php

namespace AkeneoE3\Tests\Acceptance\bootstrap;

use AkeneoE3\Domain\Repository\Query;
use AkeneoE3\Domain\Repository\ReadRepository;
use AkeneoE3\Domain\Resource\TransformableResource;

class InMemoryExtractor implements ReadRepository
{
    private TransformableResource $resource;

    public function __construct(TransformableResource $resource)
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
