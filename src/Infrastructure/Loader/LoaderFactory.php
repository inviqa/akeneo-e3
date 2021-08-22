<?php

namespace AkeneoE3\Infrastructure\Loader;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Domain\Loader;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Loader\Api\ReferenceEntity;
use AkeneoE3\Infrastructure\Loader\Api\ReferenceEntityRecord;
use AkeneoE3\Infrastructure\Loader\Api\Standard;

class LoaderFactory
{
    public function createLoader(
        ResourceType $resourceType,
        LoadProfile $loadProfile,
        AkeneoPimEnterpriseClientInterface $client
    ): Loader {
        if ($loadProfile->isDryRun() === true) {
            return new DryRunLoader();
        }

        switch ((string)$resourceType) {
            case 'reference-entity':
                return new ApiLoader(
                    new ReferenceEntity($loadProfile, $client)
                );

            case 'reference-entity-record':
                return new ApiBatchLoader(
                    new ReferenceEntityRecord($loadProfile, $client),
                    $loadProfile->getBatchSize()
                );
        }

        return new ApiBatchLoader(
            new Standard($loadProfile, $client),
            $loadProfile->getBatchSize()
        );
    }
}
