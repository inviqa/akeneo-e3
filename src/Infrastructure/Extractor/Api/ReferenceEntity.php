<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Extractor\Api;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityApiInterface;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Infrastructure\Extractor\ExtractConnector;
use AkeneoE3\Infrastructure\Extractor\Query;

final class ReferenceEntity implements ExtractConnector
{
    private ReferenceEntityApiInterface $api;

    private Query $query;

    private string $resourceType;

    public function __construct(string $resourceType, ExtractProfile $profile, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->api = $client->getReferenceEntityApi();
        $this->query = Query::fromProfile($profile, $resourceType);
    }

    public function count(): int
    {
        return 100;
    }

    /**
     * @return iterable<Resource>
     */
    public function extract(): iterable
    {
        $cursor = $this->api->all($this->query->getSearchFilters());

        foreach ($cursor as $resource) {
            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }
}
