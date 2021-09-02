<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository\Asset;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\AssetManager\AssetApiInterface;
use Akeneo\PimEnterprise\ApiClient\Api\AssetManager\AssetFamilyApiInterface;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\TransformableResource;
use AkeneoE3\Domain\Resource\ResourceCollection;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use AkeneoE3\Infrastructure\Api\Repository\DependantResourceApi;
use AkeneoE3\Infrastructure\Api\Repository\ReadResourcesRepository;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourcesRepository;
use AkeneoE3\Infrastructure\WriteResultFactory;

final class Asset implements ReadResourcesRepository, WriteResourcesRepository, DependantResourceApi
{
    private AssetFamilyApiInterface $familyApi;

    private AssetApiInterface $api;

    private ResourceType $resourceType;

    public function __construct(ResourceType $resourceType, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->familyApi = $client->getAssetFamilyApi();
        $this->api = $client->getAssetManagerApi();
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
        // Get given asset family code or fetch all families
        $families = $query->hasValue(ResourceType::ASSET_FAMILY_CODE_FIELD) === true ?
            [['code' => $query->getValue(ResourceType::ASSET_FAMILY_CODE_FIELD)]] :
            $this->familyApi->all();

        foreach ($families as $family) {
            $familyCode = $family['code'];
            $cursor = $this->api->all($familyCode, $query->getSearchFilters([ResourceType::ASSET_FAMILY_CODE_FIELD]));

            foreach ($cursor as $resource) {
                $resource[ResourceType::ASSET_FAMILY_CODE_FIELD] = $familyCode;
                yield Resource::fromArray($resource, $this->resourceType);
            }
        }
    }

    public function write(ResourceCollection $resources): iterable
    {
        if ($resources->count() === 0) {
            return [];
        }

        $familyCode = $resources->getFirst()->get(Property::create(ResourceType::ASSET_FAMILY_CODE_FIELD));

        $response = $this->api->upsertList($familyCode, $resources->changes());

        return WriteResultFactory::createFromResponse($response, $resources);
    }

    public function getParentFields(): array
    {
        return [ResourceType::ASSET_FAMILY_CODE_FIELD];
    }
}
