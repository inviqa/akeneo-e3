<?php

namespace AkeneoE3\Infrastructure\Extractor;

use AkeneoE3\Domain\Resource\Resource;

interface ReadResourcesRepository
{
    public function count(ApiQuery $query): int;

    /**
     * @return iterable<Resource>
     */
    public function read(ApiQuery $query): iterable;
}
