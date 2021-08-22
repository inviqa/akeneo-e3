<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Extractor\Api;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\PimEnterprise\ApiClient\Api\ReferenceEntityRecordApiInterface;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Infrastructure\Extractor\ExtractConnector;
use AkeneoE3\Infrastructure\Extractor\Query;
use Generator;

final class ReferenceEntityRecord implements ExtractConnector
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
        return -1;
    }

    /**
     * @return iterable<Resource>
     */
    public function extract(): iterable
    {
        $entityCode = (string)$this->query->getValue('reference_entity_code');

        $cursor = $this->api->all($entityCode, $this->query->getSearchFilters());

        foreach ($cursor as $resource) {
            $resource['reference_entity_code'] = $entityCode;
            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }
}
