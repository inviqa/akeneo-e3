<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityApiInterface;
use AkeneoE3\Domain\Load\LoadResult\Failed;
use AkeneoE3\Domain\Load\LoadResult\Loaded;
use AkeneoE3\Domain\Load\LoadResult\LoadResult;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Extractor\ReadResourcesRepository;
use AkeneoE3\Infrastructure\Extractor\ApiQuery;
use AkeneoE3\Infrastructure\Loader\WriteResourceRepository;
use Exception;

final class ReferenceEntity implements ReadResourcesRepository, WriteResourceRepository
{
    private ReferenceEntityApiInterface $api;

    private ResourceType $resourceType;

    public function __construct(ResourceType $resourceType, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->api = $client->getReferenceEntityApi();
    }

    public function count(ApiQuery $query): int
    {
        return 100;
    }

    /**
     * @return iterable<Resource>
     */
    public function read(ApiQuery $query): iterable
    {
        $cursor = $this->api->all($query->getSearchFilters());

        foreach ($cursor as $resource) {
            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }

    public function write(Resource $resource, bool $patch = true): LoadResult
    {
        $entityCode = $resource->getCode();

        try {
            $this->api->upsert($entityCode, $resource->toArray(!$patch));

            return Loaded::create($resource);
        } catch (Exception $e) {
            return Failed::create($resource, $e->getMessage());
        }
    }
}
