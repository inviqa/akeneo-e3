<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository\Asset;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\AssetManager\AssetAttributeApiInterface;
use Akeneo\PimEnterprise\ApiClient\Api\AssetManager\AssetAttributeOptionApiInterface;
use Akeneo\PimEnterprise\ApiClient\Api\AssetManager\AssetFamilyApiInterface;
use AkeneoE3\Domain\Resource\ImmutableResource;
use AkeneoE3\Domain\Result\Write\Failed;
use AkeneoE3\Domain\Result\Write\Loaded;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use AkeneoE3\Infrastructure\Api\Repository\DependantResourceApi;
use AkeneoE3\Infrastructure\Api\Repository\ReadResourcesRepository;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourceRepository;
use Exception;

final class AttributeOption implements ReadResourcesRepository, WriteResourceRepository, DependantResourceApi
{
    private AssetAttributeOptionApiInterface $optionApi;

    private AssetAttributeApiInterface $attributeApi;

    private AssetFamilyApiInterface $entityApi;

    private ResourceType $resourceType;

    public function __construct(ResourceType $resourceType, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->optionApi = $client->getAssetAttributeOptionApi();
        $this->attributeApi = $client->getAssetAttributeApi();
        $this->entityApi = $client->getAssetFamilyApi();
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
        // Get given entity code or fetch all families
        $families = $query->hasValue(ResourceType::ASSET_FAMILY_CODE_FIELD) === true ?
            [['code' => $query->getValue(ResourceType::ASSET_FAMILY_CODE_FIELD)]] :
            $this->entityApi->all();

        foreach ($families as $entity) {
            $familyCode = $entity['code'];

            // Get given attribute code or fetch all attributes
            $attributes = $query->hasValue(ResourceType::ASSET_ATTRIBUTE_CODE_FIELD) === true ?
                [['code' => $query->getValue(ResourceType::ASSET_ATTRIBUTE_CODE_FIELD), 'type' =>'single_option']] :
                $this->attributeApi->all($familyCode);

            foreach ($attributes as $attribute) {
                if (in_array($attribute['type'], ['single_option', 'multiple_options']) === false) {
                    continue;
                }

                $attributeCode = $attribute['code'];

                yield from $this->readByFamilyAndAttributeCode($familyCode, $attributeCode);
            }
        }
    }

    public function write(ImmutableResource $resource, bool $patch = true): WriteResult
    {
        $familyCode = $resource->get(Property::create(ResourceType::ASSET_FAMILY_CODE_FIELD));
        $attributeCode = $resource->get(Property::create(ResourceType::ASSET_ATTRIBUTE_CODE_FIELD));
        $optionCode = $resource->getCode();

        try {
            $this->optionApi->upsert(
                $familyCode,
                $attributeCode,
                $optionCode,
                $resource->changes()
            );

            return Loaded::create($resource);
        } catch (Exception $e) {
            return Failed::create($resource, $e->getMessage());
        }
    }

    private function readByFamilyAndAttributeCode(string $entityCode, string $attributeCode): iterable
    {
        $cursor = $this->optionApi->all($entityCode, $attributeCode);

        foreach ($cursor as $resource) {
            $resource[ResourceType::ASSET_FAMILY_CODE_FIELD] = $entityCode;
            $resource[ResourceType::ASSET_ATTRIBUTE_CODE_FIELD] = $attributeCode;

            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }

    public function getParentFields(): array
    {
        return [
            ResourceType::ASSET_FAMILY_CODE_FIELD,
            ResourceType::ASSET_ATTRIBUTE_CODE_FIELD,
        ];
    }
}
