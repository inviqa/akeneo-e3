<?php

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Repository\Query;
use AkeneoE3\Domain\Repository\ReadRepository;
use AkeneoE3\Domain\Resource\Resource;

class Extractor
{
    private ReadRepository $repository;

    private Query $query;

    public function __construct(ReadRepository $repository, Query $query)
    {
        $this->repository = $repository;
        $this->query = $query;
    }

    /**
     * @return iterable<Resource>
     */
    public function extract(): iterable
    {
        return $this->repository->read($this->query);
    }

    public function count(): int
    {
        return $this->repository->count($this->query);
    }
}
