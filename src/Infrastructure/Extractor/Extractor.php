<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Extractor;

use Akeneo\Pim\ApiClient\Api\Operation\ListableResourceInterface;
use AkeneoEtl\Domain\Extractor as DomainExtractor;
use AkeneoEtl\Domain\Resource\Resource;
use Generator;

final class Extractor implements DomainExtractor
{
    private ListableResourceInterface $api;

    private array $query;

    private string $resourceType;

    public function __construct(string $resourceType, ListableResourceInterface $api, array $query)
    {
        $this->resourceType = $resourceType;
        $this->api = $api;
        $this->query = $query;
    }

    public function count(): int
    {
        return (int)$this->api
            ->listPerPage(1, true, $this->query)
            ->getCount();
    }

    /**
     * @return Generator|Resource[]
     */
    public function extract(): Generator
    {
        $cursor = $this->api->all(100, $this->query);

        foreach ($cursor as $resource) {
            yield Resource::fromArray($resource, $this->resourceType);
        }
    }
}
