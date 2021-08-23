<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository;

use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Domain\Resource\Resource;

interface WriteResourceRepository
{
    public function write(Resource $resource, bool $patch = true): WriteResult;
}
