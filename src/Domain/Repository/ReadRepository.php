<?php

declare(strict_types=1);

namespace AkeneoE3\Domain\Repository;

use AkeneoE3\Domain\Resource\Resource;

interface ReadRepository
{
    public function count(Query $query): int;

    /**
     * @return iterable<Resource>
     */
    public function read(Query $query): iterable;
}
