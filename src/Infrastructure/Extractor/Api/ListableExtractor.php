<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Extractor\Api;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\Pim\ApiClient\Api\Operation\ListableResourceInterface;
use AkeneoEtl\Domain\Extractor;
use AkeneoEtl\Domain\Profile\ExtractProfile;
use AkeneoEtl\Domain\Resource\AuditableResource;
use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Infrastructure\Api\ApiSelector;
use AkeneoEtl\Infrastructure\Extractor\Query;
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
