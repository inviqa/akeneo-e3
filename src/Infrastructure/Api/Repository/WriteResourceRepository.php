<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository;

use AkeneoE3\Domain\Resource\WritableResource;
use AkeneoE3\Domain\Result\Write\WriteResult;

interface WriteResourceRepository
{
    public function write(WritableResource $resource, bool $patch = true): WriteResult;
}
