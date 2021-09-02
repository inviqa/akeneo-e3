<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityApiInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityAttributeApiInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityAttributeOptionApiInterface;
use AkeneoE3\Domain\Resource\ImmutableResource;
use AkeneoE3\Domain\Result\Write\Failed;
use AkeneoE3\Domain\Result\Write\Loaded;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use Exception;

final class ReferenceEntityAttributeOption implements ReadResourcesRepository, WriteResourceRepository, DependantResourceApi
{
    private ReferenceEntityAttributeOptionApiInterface $optionApi;

    private ReferenceEntityAttributeApiInterface $attributeApi;

    private ReferenceEntityApiInterface $entityApi;

    private ResourceType $resourceType;

    public function __construct(ResourceType $resourceType, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->optionApi = $client->getReferenceEntityAttributeOptionApi();
        $this->attributeApi = $client->getReferenceEntityAttributeApi();
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

            // Get given attribute code or fetch all attributes
            $attributes = $query->hasValue(ResourceType::REFERENCE_ENTITY_ATTRIBUTE_CODE_FIELD) === true ?
                [['code' => $query->getValue(ResourceType::REFERENCE_ENTITY_ATTRIBUTE_CODE_FIELD), 'type' =>'single_option']] :
                $this->attributeApi->all($entityCode);

            foreach ($attributes as $attribute) {
                if (in_array($attribute['type'], ['single_option', 'multiple_options']) === false) {
                    continue;
                }

                $attributeCode = $attribute['code'];

                yield from $this->readByEntityAndAttributeCode($entityCode, $attributeCode, $query);
            }
        }
    }

    public function write(ImmutableResource $resource, bool $patch = true): WriteResult
    {
        $entityCode = $resource->get(Property::create(ResourceType::REFERENCE_ENTITY_CODE_FIELD));
        $attributeCode = $resource->get(Property::create(ResourceType::REFERENCE_ENTITY_ATTRIBUTE_CODE_FIELD));
        $optionCode = $resource->getCode();

        try {
            $this->optionApi->upsert(
                $entityCode,
                $attributeCode,
                $optionCode,
                $resource->changes()
            );

            return Loaded::create($resource);
        } catch (Exception $e) {
            return Failed::create($resource, $e->getMessage());
        }
    }

    private function readByEntityAndAttributeCode(string $entityCode, string $attributeCode, ApiQuery $query): iterable
    {
        $cursor = $this->optionApi->all($entityCode, $attributeCode);

        foreach ($cursor as $resource) {
            $resource[ResourceType::REFERENCE_ENTITY_CODE_FIELD] = $entityCode;
            $resource[ResourceType::REFERENCE_ENTITY_ATTRIBUTE_CODE_FIELD] = $attributeCode;

            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }

    public function getParentFields(): array
    {
        return [
            ResourceType::REFERENCE_ENTITY_CODE_FIELD,
            ResourceType::REFERENCE_ENTITY_ATTRIBUTE_CODE_FIELD,
        ];
    }
}
