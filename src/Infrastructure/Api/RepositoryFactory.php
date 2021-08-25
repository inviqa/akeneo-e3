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
use AkeneoE3\Infrastructure\Api\Repository\AttributeOption;
use AkeneoE3\Infrastructure\Api\Repository\DependantResourceApi;
use AkeneoE3\Infrastructure\Api\Repository\FamilyVariant;
use AkeneoE3\Infrastructure\Api\Repository\ReadResourcesRepository;
use AkeneoE3\Infrastructure\Api\Repository\ReferenceEntity;
use AkeneoE3\Infrastructure\Api\Repository\ReferenceEntityRecord;
use AkeneoE3\Infrastructure\Api\Repository\Standard;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourceRepository;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourcesRepository;
use LogicException;

final class RepositoryFactory
{
    public function createReadRepository(
        ResourceType $resourceType,
        ExtractProfile $profile,
        AkeneoPimEnterpriseClientInterface $client
    ): BaseReadRepository {
        $api = $this->createRepository($resourceType, $client);

        if (!$api instanceof ReadResourcesRepository) {
            throw new LogicException('API must support ReadResourcesRepository');
        }

        return new ReadRepository($api);
    }

    public function createWriteRepository(
        ResourceType $resourceType,
        LoadProfile $profile,
        AkeneoPimEnterpriseClientInterface $client
    ): BasePersistRepository {
        if ($profile->isDryRun() === true) {
            return new NonPersistingRepository();
        }

        $api = $this->createRepository($resourceType, $client);

        if ($api instanceof WriteResourceRepository) {
            return new PersistByOneRepository($api);
        }

        if ($api instanceof WriteResourcesRepository && $api instanceof DependantResourceApi) {
            return
                new PersistGroupRepository(
                    new PersistBatchRepository(
                        $api,
                        $profile->getBatchSize()
                    ),
                    $api->getParentFields()
                );
        }

        if ($api instanceof WriteResourcesRepository) {
            return
                new PersistBatchRepository(
                    $api,
                    $profile->getBatchSize()
                );
        }

        throw new LogicException('API is not supported for writing resources');
    }

    /**
     * @return ReadResourcesRepository|WriteResourceRepository|WriteResourcesRepository
     */
    public function createRepository(
        ResourceType $resourceType,
        AkeneoPimEnterpriseClientInterface $client
    ) {
        switch ((string)$resourceType) {
            case 'attribute-option':
                return new AttributeOption($resourceType, $client);

            case 'family-variant':
                return new FamilyVariant($resourceType, $client);

            case 'reference-entity':
                return new ReferenceEntity($resourceType, $client);

            case 'reference-entity-record':
                return new ReferenceEntityRecord($resourceType, $client);
        }

        return new Standard($resourceType, $client);
    }
}
