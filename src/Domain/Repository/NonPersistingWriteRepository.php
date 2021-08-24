<?php

declare(strict_types=1);

namespace AkeneoE3\Domain\Repository;

use AkeneoE3\Domain\Resource\ImmutableResource;

final class NonPersistingWriteRepository implements WriteRepository
{
    public function persist(ImmutableResource $resource, bool $patch): iterable
    {
        return [];
    }

    public function flush(bool $patch): iterable
    {
        return [];
    }
}
