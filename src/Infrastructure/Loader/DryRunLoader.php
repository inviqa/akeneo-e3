<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Loader;

use AkeneoE3\Domain\Repository\WriteRepository;
use AkeneoE3\Domain\Resource\Resource;

final class DryRunLoader implements WriteRepository
{
    public function persist(Resource $resource, bool $patch): iterable
    {
        return [];
    }

    public function flush(bool $patch): iterable
    {
        return [];
    }
}
