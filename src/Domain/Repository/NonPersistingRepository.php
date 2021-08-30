<?php

declare(strict_types=1);

namespace AkeneoE3\Domain\Repository;

use AkeneoE3\Domain\Resource\ImmutableResource;
use AkeneoE3\Domain\Result\Write\Loaded;
use AkeneoE3\Domain\Result\Write\Skipped;

final class NonPersistingRepository implements PersistRepository
{
    public function persist(ImmutableResource $resource, bool $patch): iterable
    {
        yield Skipped::create($resource);
    }

    public function flush(bool $patch): iterable
    {
        return [];
    }
}
