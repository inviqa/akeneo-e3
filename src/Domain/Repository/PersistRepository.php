<?php

declare(strict_types=1);

namespace AkeneoE3\Domain\Repository;

use AkeneoE3\Domain\Resource\WritableResource;
use AkeneoE3\Domain\Result\Write\WriteResult;

interface PersistRepository
{
    /**
     * @return iterable<WriteResult>
     */
    public function persist(WritableResource $resource, bool $patch): iterable;

    /**
     * Finish persist, e.g. save remaining resources in bulk.
     *
     * @return iterable<WriteResult>
     */
    public function flush(bool $patch): iterable;
}
