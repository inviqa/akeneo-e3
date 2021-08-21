<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Extractor;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Domain\Extractor;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Infrastructure\Extractor\Api\ListableExtractor;
use AkeneoE3\Infrastructure\Extractor\Api\ReferenceEntityExtractor;
use AkeneoE3\Infrastructure\Extractor\Api\ReferenceEntityRecordExtractor;
use LogicException;

final class ExtractorFactory
{
    public function create(
        string $resourceType,
        ExtractProfile $profile,
        AkeneoPimClientInterface $client
    ): Extractor {
        switch ($resourceType) {
            case 'reference-entity':
                if (!$client instanceof AkeneoPimEnterpriseClientInterface) {
                    throw new LogicException(sprintf('%s is supported only in Enterprise Edition', $resourceType));
                }

                return new ReferenceEntityExtractor($resourceType, $profile, $client);

            case 'reference-entity-record':
                if (!$client instanceof AkeneoPimEnterpriseClientInterface) {
                    throw new LogicException(sprintf('%s is supported only in Enterprise Edition', $resourceType));
                }

                return new ReferenceEntityRecordExtractor($resourceType, $profile, $client);
        }

        return new ListableExtractor($resourceType, $profile, $client);
    }
}
