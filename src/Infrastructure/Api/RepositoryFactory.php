<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\Repository\NonPersistingRepository;
use AkeneoE3\Domain\Repository\ReadRepository as BaseReadRepository;
use AkeneoE3\Domain\Repository\PersistRepository as BasePersistRepository;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Repository\FamilyVariant;
use AkeneoE3\Infrastructure\Api\Repository\ReferenceEntity;
use AkeneoE3\Infrastructure\Api\Repository\ReferenceEntityRecord;
use AkeneoE3\Infrastructure\Api\Repository\Standard;

final class RepositoryFactory
{
    public function createReadRepository(
        ResourceType $resourceType,
        ExtractProfile $profile,
        AkeneoPimEnterpriseClientInterface $client
    ): BaseReadRepository {

        // @todo: extract createRepository method and then check by interface with what Read/WriteRepository to wrap
        switch ((string)$resourceType) {
            case 'family-variant':
                return new ReadRepository(new FamilyVariant($resourceType, $client));

            case 'reference-entity':
                return new ReadRepository(new ReferenceEntity($resourceType, $client));

            case 'reference-entity-record':
                return new ReadRepository(new ReferenceEntityRecord($resourceType, $client));
        }

        return new ReadRepository(new Standard($resourceType, $client));
    }

    public function createWriteRepository(
        ResourceType $resourceType,
        LoadProfile $profile,
        AkeneoPimEnterpriseClientInterface $client
    ): BasePersistRepository {
        if ($profile->isDryRun() === true) {
            return new NonPersistingRepository();
        }

        switch ((string)$resourceType) {
            case 'family-variant':
                return
                    new PersistGroupRepository(
                        new PersistBatchRepository(
                            new FamilyVariant($resourceType, $client),
                            $profile->getBatchSize()
                        ),
                        [ResourceType::FAMILY_CODE_FIELD]
                    );

            case 'reference-entity':
                return new PersistRepository(
                    new ReferenceEntity($resourceType, $client)
                );

            case 'reference-entity-record':
                new PersistGroupRepository(
                    new PersistBatchRepository(
                        new ReferenceEntityRecord($resourceType, $client),
                        $profile->getBatchSize()
                    ),
                    [ResourceType::REFERENCE_ENTITY_CODE_FIELD]
                );
        }

        return new PersistBatchRepository(
            new Standard($resourceType, $client),
            $profile->getBatchSize()
        );
    }
}
