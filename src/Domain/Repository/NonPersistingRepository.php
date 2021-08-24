<?php

declare(strict_types=1);

namespace AkeneoE3\Domain\Repository;

use AkeneoE3\Domain\Resource\ImmutableResource;

final class NonPersistingRepository implements PersistRepository
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
