<?php

declare(strict_types=1);

namespace AkeneoE3\Domain\Repository;

use AkeneoE3\Domain\Load\LoadResult\LoadResult;
use AkeneoE3\Domain\Resource\Resource;

interface WriteRepository
{
    /**
     * @return LoadResult[]
     */
    public function persist(Resource $resource, bool $patch): iterable;

    /**
     * Finish persist, e.g. save remaining resources in bulk.
     *
     * @return LoadResult[]
     */
    public function flush(bool $patch): iterable;
}
