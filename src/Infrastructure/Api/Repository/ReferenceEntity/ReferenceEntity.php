<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository\ReferenceEntity;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityApiInterface;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\WritableResource;
use AkeneoE3\Domain\Resource\TransformableResource;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Domain\Result\Write\Failed;
use AkeneoE3\Domain\Result\Write\Loaded;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use AkeneoE3\Infrastructure\Api\Repository\ReadResourcesRepository;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourceRepository;
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
        return -1;
    }

    /**
     * @return iterable<TransformableResource>
     */
    public function read(ApiQuery $query): iterable
    {
        $cursor = $this->api->all($query->getSearchFilters([]));

        foreach ($cursor as $resource) {
            yield Resource::fromArray($resource, $this->resourceType);
        }
    }

    public function write(WritableResource $resource): WriteResult
    {
        $entityCode = $resource->getCode();

        try {
            $this->api->upsert($entityCode, $resource->changes()->toArray());

            return Loaded::create($resource);
        } catch (Exception $e) {
            return Failed::create($resource, $e->getMessage());
        }
    }
}
