<?php

namespace AkeneoEtl\Tests\Acceptance\bootstrap;

use AkeneoEtl\Domain\Extractor;
use AkeneoEtl\Domain\Resource\Resource;
use Generator;

class InMemoryExtractor implements Extractor
{
    private \AkeneoEtl\Domain\Resource\Resource $resource;

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
    public function extract(): Generator
    {
        yield $this->resource;
    }
}
