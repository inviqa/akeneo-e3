<?php

namespace AkeneoE3\Infrastructure\Api\Repository;

use AkeneoE3\Domain\Resource\TransformableResource;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;

interface ReadResourcesRepository
{
    public function count(ApiQuery $query): int;

    /**
     * @return iterable<TransformableResource>
     */
    public function read(ApiQuery $query): iterable;
}
