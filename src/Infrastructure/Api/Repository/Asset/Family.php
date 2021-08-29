<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository\Asset;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\AssetManager\AssetFamilyApiInterface;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\ImmutableResource;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Domain\Result\Write\Failed;
use AkeneoE3\Domain\Result\Write\Loaded;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use AkeneoE3\Infrastructure\Api\Repository\ReadResourcesRepository;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourceRepository;
use Exception;

final class Family implements ReadResourcesRepository, WriteResourceRepository
{
    private AssetFamilyApiInterface $api;

    private ResourceType $resourceType;

    public function __construct(ResourceType $resourceType, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->api = $client->getAssetFamilyApi();
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
        $cursor = $this->api->all($query->getSearchFilters([]));

        foreach ($cursor as $resource) {
            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }

    public function write(ImmutableResource $resource, bool $patch = true): WriteResult
    {
        $assetFamilyCode = $resource->getCode();

        try {
            $data = $resource->toArray(!$patch);
            //@todo: temp solution is to unset "attribute_as_main_media" and "product_link_rules"
            // in duplicate mode
            // it should first copy assets without any fields
            // then attributes
            // then assets with all fields
            unset($data['attribute_as_main_media']);
            unset($data['product_link_rules']);

            $this->api->upsert($assetFamilyCode, $data);

            return Loaded::create($resource);
        } catch (Exception $e) {
            return Failed::create($resource, $e->getMessage());
        }
    }
}
