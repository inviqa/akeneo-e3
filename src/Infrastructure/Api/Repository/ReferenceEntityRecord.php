<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityRecordApiInterface;
use AkeneoE3\Domain\Load\LoadResult\LoadResult;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceCollection;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use AkeneoE3\Infrastructure\Loader\LoadResultFactory;

final class ReferenceEntityRecord implements ReadResourcesRepository, WriteResourcesRepository
{
    private ReferenceEntityRecordApiInterface $api;

    private ResourceType $resourceType;

    public function __construct(ResourceType $resourceType, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->api = $client->getReferenceEntityRecordApi();
    }

    public function count(ApiQuery $query): int
    {
        return -1;
    }

    /**
     * @return iterable<Resource>
     */
    public function read(ApiQuery $query): iterable
    {
        $entityCode = (string)$query->getValue('reference_entity_code');

        $cursor = $this->api->all($entityCode, $query->getSearchFilters());

        foreach ($cursor as $resource) {
            $resource['reference_entity_code'] = $entityCode;
            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }

    /**
     * @return LoadResult[]
     */
    public function write(ResourceCollection $resources, bool $patch = true): iterable
    {
        if ($resources->count() === 0) {
            return [];
        }

        $entityCode = $resources->getFirst()->get(Property::create('reference_entity_code'));

        $response = $this->api->upsertList($entityCode, $resources->toArray(!$patch));

        yield from LoadResultFactory::createFromResponse($response, $resources);
    }
}
