<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Extractor\Api;

use Akeneo\Pim\ApiClient\Api\Operation\ListableResourceInterface;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\ApiSelector;
use AkeneoE3\Infrastructure\Extractor\ExtractConnector;
use AkeneoE3\Infrastructure\Extractor\Query;
use LogicException;

final class Standard implements ExtractConnector
{
    private ResourceType $resourceType;

    private Query $query;

    private ListableResourceInterface $api;

    public function __construct(ResourceType $resourceType, ExtractProfile $profile, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;

        $api = (new ApiSelector())->getApi($client, $resourceType);

        if (!$api instanceof ListableResourceInterface) {
            throw new LogicException(sprintf('%s API does not support listing', $resourceType));
        }

        $this->api = $api;

        $this->query = Query::fromProfile($profile, $resourceType);
    }

    public function count(): int
    {
        return (int)$this->api
            ->listPerPage(1, true, $this->query->getSearchFilters())
            ->getCount();
    }

    /**
     * @return iterable<Resource>
     */
    public function extract(): iterable
    {
        $cursor = $this->api->all(100, $this->query->getSearchFilters());

        foreach ($cursor as $resource) {
            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }
}
