<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository;

use Akeneo\Pim\ApiClient\Api\Operation\ListableResourceInterface;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceCollection;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\ApiSelector;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use AkeneoE3\Infrastructure\WriteResultFactory;
use LogicException;

final class Standard implements ReadResourcesRepository, WriteResourcesRepository
{
    private ResourceType $resourceType;

    /**
     * @var ListableResourceInterface&UpsertableResourceListInterface
     */
    private $api;

    public function __construct(ResourceType $resourceType, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;

        $api = (new ApiSelector())->getApi($client, $resourceType);

        if (!$api instanceof ListableResourceInterface) {
            throw new LogicException(sprintf('%s API does not support listing', $resourceType));
        }

        if (!$api instanceof UpsertableResourceListInterface) {
            throw new LogicException(sprintf('%s API does not support upsert list', $resourceType));
        }

        $this->api = $api;
    }

    public function count(ApiQuery $query): int
    {
        return (int)$this->api
            ->listPerPage(1, true, $query->getSearchFilters([]))
            ->getCount();
    }

    /**
     * @return iterable<Resource>
     */
    public function read(ApiQuery $query): iterable
    {
        $cursor = $this->api->all(100, $query->getSearchFilters([]));

        foreach ($cursor as $resource) {
            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }

    /**
     * @return iterable<WriteResult>
     */
    public function write(ResourceCollection $resources, bool $patch = true): iterable
    {
        if ($resources->count() === 0) {
            return [];
        }

        $response = $this->api->upsertList($resources->toArray(!$patch));

        return WriteResultFactory::createFromResponse($response, $resources);
    }
}
