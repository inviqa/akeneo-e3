<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Loader;

use AkeneoE3\Domain\Load\LoadResult\LoadResult;
use AkeneoE3\Domain\Resource\ResourceCollection;

interface LoadListConnector
{
    /**
     * @return LoadResult[]
     */
    public function load(ResourceCollection $resources): iterable;
}
