<?php

namespace AkeneoE3\Infrastructure\Api;

use AkeneoE3\Domain\Repository\WriteRepository;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceCollection;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourcesRepository;

class ApiWriteBatchRepository implements WriteRepository
{
    private WriteResourcesRepository $connector;

    private ResourceCollection $buffer;

    private int $batchSize;

    public function __construct(WriteResourcesRepository $repository, int $batchSize)
    {
        $this->connector = $repository;
        $this->batchSize = $batchSize;

        $this->buffer = new ResourceCollection();
    }

    public function persist(Resource $resource, bool $patch): iterable
    {
        $this->buffer->add($resource);

        if ($this->buffer->count() === $this->batchSize) {
            yield from $this->flush($patch);
        }

        return [];
    }

    public function flush(bool $patch): iterable
    {
        yield from $this->connector->write($this->buffer, $patch);

        $this->buffer->clear();
    }
}
