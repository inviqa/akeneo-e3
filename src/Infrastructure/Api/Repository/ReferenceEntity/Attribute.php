<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository\ReferenceEntity;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityApiInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityAttributeApiInterface;
use AkeneoE3\Domain\Resource\WritableResource;
use AkeneoE3\Domain\Result\Write\Failed;
use AkeneoE3\Domain\Result\Write\Loaded;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\TransformableResource;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use AkeneoE3\Infrastructure\Api\Repository\DependantResourceApi;
use AkeneoE3\Infrastructure\Api\Repository\ReadResourcesRepository;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourceRepository;
use Exception;

final class Attribute implements ReadResourcesRepository, WriteResourceRepository, DependantResourceApi
{
    private ReferenceEntityAttributeApiInterface $attributeApi;

    private ReferenceEntityApiInterface $entityApi;

    private ResourceType $resourceType;

    public function __construct(ResourceType $resourceType, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->attributeApi = $client->getReferenceEntityAttributeApi();
        $this->entityApi = $client->getReferenceEntityApi();
    }

    public function count(ApiQuery $query): int
    {
        return -1;
    }

    /**
     * @return iterable<TransformableResource>
     */
    public function read(ApiQuery $query): iterable
    {
        // Get given entity code or fetch all entities
        $entities = $query->hasValue(ResourceType::REFERENCE_ENTITY_CODE_FIELD) === true ?
            [['code' => $query->getValue(ResourceType::REFERENCE_ENTITY_CODE_FIELD)]] :
            $this->entityApi->all();

        foreach ($entities as $entity) {
            yield from $this->readByEntityCode($entity['code'], $query);
        }
    }

    public function write(WritableResource $resource): WriteResult
    {
        $entityCode = $resource->get(Property::create(ResourceType::REFERENCE_ENTITY_CODE_FIELD));
        $attributeCode = $resource->getCode();

        try {
            $data = $resource->changes()->toArray();

            // fix empty labels from reader
            if (count($data['labels']) === 0) {
                unset($data['labels']);
            }

            $this->attributeApi->upsert(
                $entityCode,
                $attributeCode,
                $data
            );

            return Loaded::create($resource);
        } catch (Exception $e) {
            return Failed::create($resource, $e->getMessage());
        }
    }

    private function readByEntityCode(string $entityCode, ApiQuery $query): iterable
    {
        $cursor = $this->attributeApi->all($entityCode, $query->getSearchFilters([ResourceType::REFERENCE_ENTITY_CODE_FIELD]));

        foreach ($cursor as $resource) {
            $resource[ResourceType::REFERENCE_ENTITY_CODE_FIELD] = $entityCode;
            yield Resource::fromArray($resource, $this->resourceType);
        }
    }

    public function getParentFields(): array
    {
        return [ResourceType::REFERENCE_ENTITY_CODE_FIELD];
    }
}
