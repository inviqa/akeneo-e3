<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository\Asset;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\AssetManager\AssetAttributeApiInterface;
use Akeneo\PimEnterprise\ApiClient\Api\AssetManager\AssetFamilyApiInterface;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\ImmutableResource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Domain\Result\Write\Failed;
use AkeneoE3\Domain\Result\Write\Loaded;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use AkeneoE3\Infrastructure\Api\Repository\DependantResourceApi;
use AkeneoE3\Infrastructure\Api\Repository\ReadResourcesRepository;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourceRepository;
use Exception;

final class Attribute implements ReadResourcesRepository, WriteResourceRepository, DependantResourceApi
{
    private AssetFamilyApiInterface $familyApi;

    private AssetAttributeApiInterface $api;

    private ResourceType $resourceType;

    public function __construct(ResourceType $resourceType, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->familyApi = $client->getAssetFamilyApi();
        $this->api = $client->getAssetAttributeApi();
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
            $this->familyApi->all();

        foreach ($families as $family) {
            yield from $this->readByFamilyCode($family['code'], $query);
        }
    }

    public function write(ImmutableResource $resource, bool $patch = true): WriteResult
    {
        $familyCode = $resource->get(Property::create(ResourceType::ASSET_FAMILY_CODE_FIELD));
        $attributeCode = $resource->getCode();

        try {
            $this->api->upsert(
                $familyCode,
                $attributeCode,
                $resource->changes()
            );

            return Loaded::create($resource);
        } catch (Exception $e) {
            return Failed::create($resource, $e->getMessage());
        }
    }

    private function readByFamilyCode(string $familyCode, ApiQuery $query): iterable
    {
        $cursor = $this->api->all($familyCode, $query->getSearchFilters([ResourceType::ASSET_FAMILY_CODE_FIELD]));

        foreach ($cursor as $resource) {
            $resource[ResourceType::ASSET_FAMILY_CODE_FIELD] = $familyCode;
            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }

    public function getParentFields(): array
    {
        return [ResourceType::ASSET_FAMILY_CODE_FIELD];
    }
}
