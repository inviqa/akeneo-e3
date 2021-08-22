<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Extractor;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Domain\Extractor;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Extractor\Api\Standard;
use AkeneoE3\Infrastructure\Extractor\Api\ReferenceEntity;
use AkeneoE3\Infrastructure\Extractor\Api\ReferenceEntityRecord;

final class ExtractorFactory
{
    public function create(
        ResourceType $resourceType,
        ExtractProfile $profile,
        AkeneoPimEnterpriseClientInterface $client
    ): Extractor {
        switch ((string)$resourceType) {
            case 'reference-entity':
                return new ApiExtractor(new ReferenceEntity($resourceType, $profile, $client));

            case 'reference-entity-record':
                return new ApiExtractor(new ReferenceEntityRecord($resourceType, $profile, $client));
        }

        return new ApiExtractor(new Standard($resourceType, $profile, $client));
    }
}
