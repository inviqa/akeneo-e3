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
use AkeneoE3\Infrastructure\Api\Repository\Asset;
use AkeneoE3\Infrastructure\Api\Repository\AttributeOption;
use AkeneoE3\Infrastructure\Api\Repository\DependantResourceApi;
use AkeneoE3\Infrastructure\Api\Repository\FamilyVariant;
use AkeneoE3\Infrastructure\Api\Repository\ReadResourcesRepository;
use AkeneoE3\Infrastructure\Api\Repository\ReferenceEntity;
use AkeneoE3\Infrastructure\Api\Repository\Standard;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourceRepository;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourcesRepository;
use LogicException;

final class RepositoryFactory
{
    public function createReadRepository(
        ResourceType $resourceType,
        AkeneoPimEnterpriseClientInterface $client
    ): BaseReadRepository {
        $api = $this->createRepository($resourceType, $client);

        if (!$api instanceof ReadResourcesRepository) {
            throw new LogicException(
                'API must support ReadResourcesRepository'
            );
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
        $apis = [
            'attribute-option' => function ($resourceType, $client) {
                return new AttributeOption($resourceType, $client);
            },

            'family-variant' => function ($resourceType, $client) {
                return new FamilyVariant($resourceType, $client);
            },

            'reference-entity' => function ($resourceType, $client) {
                return new ReferenceEntity\ReferenceEntity($resourceType, $client);
            },

            'reference-entity-attribute' => function ($resourceType, $client) {
                return new ReferenceEntity\Attribute($resourceType, $client);
            },

            'reference-entity-attribute-option' => function ($resourceType, $client) {
                return new ReferenceEntity\AttributeOption($resourceType, $client);
            },

            'reference-entity-record' => function ($resourceType, $client) {
                return new ReferenceEntity\Record($resourceType, $client);
            },

            'asset-family' => function ($resourceType, $client) {
                return new Asset\Family($resourceType, $client);
            },

            'asset-attribute' => function ($resourceType, $client) {
                return new Asset\Attribute($resourceType, $client);
            },

            'asset-attribute-option' => function ($resourceType, $client) {
                return new Asset\AttributeOption($resourceType, $client);
            },

            'asset' => function ($resourceType, $client) {
                return new Asset\Asset($resourceType, $client);
            },
        ];

        return isset($apis[(string)$resourceType]) ?
            $apis[(string)$resourceType]($resourceType, $client) :
            new Standard($resourceType, $client);
    }
}
