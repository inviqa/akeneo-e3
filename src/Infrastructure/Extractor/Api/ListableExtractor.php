<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Extractor\Api;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\Pim\ApiClient\Api\Operation\ListableResourceInterface;
use Akeneo\Pim\ApiClient\Search\SearchBuilder;
use AkeneoEtl\Domain\Extractor;
use AkeneoEtl\Domain\Profile\ExtractProfile;
use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Infrastructure\Api\ApiSelector;
use Generator;
use LogicException;

final class ListableExtractor implements Extractor
{
    private string $resourceType;

    private ExtractProfile $profile;

    private array $query;

    public function __construct(string $resourceType, ExtractProfile $profile, AkeneoPimClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->profile = $profile;
        $this->api = (new ApiSelector())->getApi($client, $resourceType);

        if (!$this->api instanceof ListableResourceInterface) {
            throw new LogicException(sprintf('%s API does not support listing', $resourceType));
        }

        $this->query = $this->buildQuery($profile->getConditions());
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
