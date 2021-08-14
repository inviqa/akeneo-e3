<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Extractor;

use Akeneo\Pim\ApiClient\Api\Operation\ListableResourceInterface;
use Akeneo\Pim\ApiClient\Search\SearchBuilder;
use AkeneoEtl\Domain\Extractor;
use AkeneoEtl\Domain\Resource\Resource;
use Generator;

final class ApiExtractor implements Extractor
{
    private ListableResourceInterface $api;

    private array $query;

    private string $resourceType;

    public function __construct(string $resourceType, ListableResourceInterface $api, array $conditions)
    {
        $this->resourceType = $resourceType;
        $this->api = $api;
        $this->query = $this->buildQuery($conditions);
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

    private function buildQuery(array $conditions): array
    {
        $searchBuilder = new SearchBuilder();

        foreach ($conditions as $condition) {
            $searchBuilder
                ->addFilter(
                    $condition['field'],
                    $condition['operator'],
                    $condition['value']
                );
        }

        $searchFilters = $searchBuilder->getFilters();

        return ['search' => $searchFilters];
    }
}
