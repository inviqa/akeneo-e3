<?php

declare(strict_types=1);

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Load\LoadResult\LoadResult;
use AkeneoE3\Domain\Resource\Resource;

interface Loader
{
    /**
     * @return array|LoadResult[]
     */
    public function load(Resource $resource): array;

    /**
     * Finish loading, e.g. load remaining resources in bulk.
     *
     * @return array|LoadResult[]
     */
    public function finish(): array;
}
