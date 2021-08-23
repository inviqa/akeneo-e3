<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository;

use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Domain\Resource\ResourceCollection;

interface WriteResourcesRepository
{
    /**
     * @return iterable<WriteResult>
     */
    public function write(ResourceCollection $resources, bool $patch = true): iterable;
}
