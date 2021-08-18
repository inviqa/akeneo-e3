<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Extractor\Api;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\Pim\ApiClient\Search\SearchBuilder;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityRecordApiInterface;
use AkeneoEtl\Domain\Extractor;
use AkeneoEtl\Domain\Profile\ExtractProfile;
use AkeneoEtl\Domain\Resource\Resource;
use Generator;

final class ReferenceEntityRecordExtractor implements Extractor
{
    private ReferenceEntityRecordApiInterface $api;

    private array $query;

    private string $resourceType;

    public function __construct(string $resourceType, ExtractProfile $profile, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->api = $client->getReferenceEntityRecordApi();
        $this->query = $this->buildQuery($profile->getConditions());
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
        $cursor = $this->api->all('suppliers', []);

        foreach ($cursor as $resource) {
            $resource['reference_entity_code'] = 'suppliers';
            yield Resource::fromArray($resource, $this->resourceType);
        }
    }

    private function buildQuery(array $conditions): array
    {
        // remove "code"
        return ['search' => $conditions];
    }
}
