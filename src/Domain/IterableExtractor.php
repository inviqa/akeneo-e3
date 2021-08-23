<?php

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Repository\Query;
use AkeneoE3\Domain\Repository\QueryFactory;
use AkeneoE3\Domain\Repository\ReadRepository;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceType;

class IterableExtractor
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
