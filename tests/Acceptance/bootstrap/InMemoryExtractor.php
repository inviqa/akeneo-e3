<?php

namespace AkeneoE3\Tests\Acceptance\bootstrap;

use AkeneoE3\Domain\Extractor;
use AkeneoE3\Domain\Resource\Resource;

class InMemoryExtractor implements Extractor
{
    private Resource $resource;

    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function count(): int
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function extract(): iterable
    {
        yield $this->resource;
    }
}
