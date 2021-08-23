<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\Repository\ReadRepository;
use AkeneoE3\Domain\Repository\WriteRepository;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Repository\ReferenceEntity;
use AkeneoE3\Infrastructure\Api\Repository\ReferenceEntityRecord;
use AkeneoE3\Infrastructure\Api\Repository\Standard;
use AkeneoE3\Infrastructure\Extractor\ApiReadRepository;
use AkeneoE3\Infrastructure\Loader\ApiBatchLoader;
use AkeneoE3\Infrastructure\Loader\ApiLoader;
use AkeneoE3\Infrastructure\Loader\DryRunLoader;

final class RepositoryFactory
{
    public function createReadRepository(
        ResourceType $resourceType,
        ExtractProfile $profile,
        AkeneoPimEnterpriseClientInterface $client
    ): ReadRepository {
        switch ((string)$resourceType) {
            case 'reference-entity':
                return new ApiReadRepository(new ReferenceEntity($resourceType, $client));

            case 'reference-entity-record':
                return new ApiReadRepository(new ReferenceEntityRecord($resourceType, $client));
        }

        return new ApiReadRepository(new Standard($resourceType, $client));
    }

    public function createWriteRepository(
        ResourceType $resourceType,
        LoadProfile $profile,
        AkeneoPimEnterpriseClientInterface $client
    ): WriteRepository {
        if ($profile->isDryRun() === true) {
            return new DryRunLoader();
        }

        switch ((string)$resourceType) {
            case 'reference-entity':
                return new ApiLoader(
                    new ReferenceEntity($resourceType, $client)
                );

            case 'reference-entity-record':
                return new ApiBatchLoader(
                    new ReferenceEntityRecord($resourceType, $client),
                    $profile->getBatchSize()
                );
        }

        return new ApiBatchLoader(
            new Standard($resourceType, $client),
            $profile->getBatchSize()
        );
    }
}
