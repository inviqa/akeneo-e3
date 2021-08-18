<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Extractor;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\Pim\ApiClient\Api\Operation\ListableResourceInterface;
use Akeneo\Pim\ApiClient\Search\SearchBuilder;
use AkeneoEtl\Domain\Extractor;
use AkeneoEtl\Domain\Profile\ExtractProfile;
use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Infrastructure\Api\ApiSelector;
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
        $apiSelector = new ApiSelector();

        switch ($resourceType) {
            case 'reference-entity-record':
                return new ReferenceEntityRecordExtractor(
                    $resourceType,
                    $apiSelector->getApi($client, $resourceType),
                    $profile->getConditions()
                );
        }

        $api = $apiSelector->getApi($client, $resourceType);

        // @todo: move it to an extractor
        if (!$api instanceof ListableResourceInterface) {
            throw new LogicException(sprintf('%s API does not support listing', $resourceType));
        }

        return new ListableExtractor(
            $resourceType,
            $apiSelector->getApi($client, $resourceType),
            $profile->getConditions()
        );
    }
}
