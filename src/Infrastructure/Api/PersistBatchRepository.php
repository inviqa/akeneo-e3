<?php

namespace AkeneoE3\Infrastructure\Api;

use AkeneoE3\Domain\Repository\PersistRepository;
use AkeneoE3\Domain\Resource\WritableResource;
use AkeneoE3\Domain\Resource\ResourceCollection;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourcesRepository;

class PersistBatchRepository implements PersistRepository
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

    public function persist(WritableResource $resource): iterable
    {
        $this->buffer->add($resource);

        if ($this->buffer->count() === $this->batchSize) {
            yield from $this->flush();
        } else {
            return [];
        }
    }

    public function flush(): iterable
    {
        yield from $this->connector->write($this->buffer);

        $this->buffer->clear();
    }

    public function bufferSize(): int
    {
        return $this->buffer->count();
    }
}
