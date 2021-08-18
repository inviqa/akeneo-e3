<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Extractor;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoEtl\Domain\Extractor;
use AkeneoEtl\Domain\Profile\ExtractProfile;
use AkeneoEtl\Infrastructure\Extractor\Api\ListableExtractor;
use AkeneoEtl\Infrastructure\Extractor\Api\ReferenceEntityRecordExtractor;
use LogicException;

final class ExtractorFactory
{
    public function create(
        string $resourceType,
        ExtractProfile $profile,
        AkeneoPimClientInterface $client
    ): Extractor {
        switch ($resourceType) {
            case 'reference-entity-record':

                if (!$client instanceof AkeneoPimEnterpriseClientInterface) {
                    throw new LogicException(sprintf('%s is supported only in Enterprise Edition', $resourceType));
                }

                return new ReferenceEntityRecordExtractor($resourceType, $profile, $client);
        }

        return new ListableExtractor($resourceType, $profile, $client);
    }
}
