<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Extractor\Api;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityRecordApiInterface;
use AkeneoEtl\Domain\Extractor;
use AkeneoEtl\Domain\Profile\ExtractProfile;
use AkeneoEtl\Domain\Resource\AuditableResource;
use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Infrastructure\Extractor\Query;
use Generator;

final class ReferenceEntityRecordExtractor implements Extractor
{
    private ReferenceEntityRecordApiInterface $api;

    private Query $query;

    private string $resourceType;

    public function __construct(string $resourceType, ExtractProfile $profile, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->api = $client->getReferenceEntityRecordApi();
        $this->query = Query::fromProfile($profile, $resourceType);
    }

    public function count(): int
    {
        return 100;
    }

    /**
     * @return Generator|Resource[]
     */
    public function extract(): Generator
    {
        $cursor = $this->api->all('suppliers', $this->query->toArray());

        foreach ($cursor as $resource) {
            $resource['reference_entity_code'] = 'suppliers';
            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }
}
