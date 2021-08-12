<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

use AkeneoEtl\Domain\Load\LoadResult\LoadResult;
use AkeneoEtl\Domain\Resource\Resource;

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
