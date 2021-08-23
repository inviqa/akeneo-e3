<?php

namespace AkeneoE3\Infrastructure\Loader;

use AkeneoE3\Domain\Repository\WriteRepository;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceCollection;

class ApiBatchLoader implements WriteRepository
{
    private LoadListConnector $connector;

    private ResourceCollection $buffer;

    private int $batchSize;

    public function __construct(LoadListConnector $connector, int $batchSize)
    {
        $this->connector = $connector;
        $this->batchSize = $batchSize;

        $this->buffer = new ResourceCollection();
    }

    public function persist(Resource $resource): iterable
    {
        $this->buffer->add($resource);

        if ($this->buffer->count() === $this->batchSize) {
            yield from $this->flush();
        }

        return [];
    }

    public function flush(): iterable
    {
        yield from $this->connector->load($this->buffer);

        $this->buffer->clear();
    }
}
