<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityApiInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityRecordApiInterface;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceCollection;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use AkeneoE3\Infrastructure\WriteResultFactory;

final class ReferenceEntityRecord implements ReadResourcesRepository, WriteResourcesRepository, DependantResourceApi
{
    private ReferenceEntityRecordApiInterface $recordApi;

    private ResourceType $resourceType;

    private ReferenceEntityApiInterface $entityApi;

    public function __construct(ResourceType $resourceType, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->recordApi = $client->getReferenceEntityRecordApi();
        $this->entityApi = $client->getReferenceEntityApi();
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
        // Get given entity code or fetch all entities
        $entities = $query->hasValue(ResourceType::REFERENCE_ENTITY_CODE_FIELD) === true ?
            [['code' => $query->getValue(ResourceType::REFERENCE_ENTITY_CODE_FIELD)]] :
            $this->entityApi->all();

        foreach ($entities as $entity) {
            $entityCode = $entity['code'];

            $cursor = $this->recordApi->all(
                $entityCode,
                $query->getSearchFilters(
                    [ResourceType::REFERENCE_ENTITY_CODE_FIELD]
                )
            );

            foreach ($cursor as $resource) {
                $resource[ResourceType::REFERENCE_ENTITY_CODE_FIELD] = $entityCode;
                yield AuditableResource::fromArray(
                    $resource,
                    $this->resourceType
                );
            }
        }
    }

    /**
     * @return WriteResult[]
     */
    public function write(ResourceCollection $resources, bool $patch = true): iterable
    {
        if ($resources->count() === 0) {
            return [];
        }

        $entityCode = $resources->getFirst()->get(Property::create(ResourceType::REFERENCE_ENTITY_CODE_FIELD));

        $response = $this->recordApi->upsertList($entityCode, $resources->toArray());

        return WriteResultFactory::createFromResponse($response, $resources);
    }

    public function getParentFields(): array
    {
        return [ResourceType::REFERENCE_ENTITY_CODE_FIELD];
    }
}
