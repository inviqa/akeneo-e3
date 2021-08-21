<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Extractor\Api;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\Pim\ApiClient\Api\Operation\ListableResourceInterface;
use AkeneoE3\Domain\Extractor;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Infrastructure\Api\ApiSelector;
use AkeneoE3\Infrastructure\Extractor\Query;
use Generator;
use LogicException;

final class ListableExtractor implements Extractor
{
    private string $resourceType;

    private Query $query;

    private ListableResourceInterface $api;

    public function __construct(string $resourceType, ExtractProfile $profile, AkeneoPimClientInterface $client)
    {
        $this->resourceType = $resourceType;

        $api = (new ApiSelector())->getApi($client, $resourceType);
        if (!$api instanceof ListableResourceInterface) {
            throw new LogicException(sprintf('%s API does not support listing', $resourceType));
        }
        $this->api = $api;

        $this->query = Query::fromProfile($profile, $resourceType);
    }

    public function count(): int
    {
        return (int)$this->api
            ->listPerPage(1, true, $this->query->toArray())
            ->getCount();
    }

    /**
     * @return Generator|Resource[]
     */
    public function extract(): Generator
    {
        $cursor = $this->api->all(100, $this->query->toArray());

        foreach ($cursor as $resource) {
            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }
}
