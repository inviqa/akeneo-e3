<?php

namespace AkeneoEtl\Infrastructure\Loader;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Profile\LoadProfile;
use AkeneoEtl\Infrastructure\Loader\Api\ReferenceEntityRecordLoader;
use AkeneoEtl\Infrastructure\Loader\Api\UpsertableLoader;
use LogicException;

class LoaderFactory
{
    public function createLoader(
        string $resourceType,
        LoadProfile $loadProfile,
        AkeneoPimClientInterface $client
    ): Loader {
        if ($loadProfile->isDryRun() === true) {
            return new DryRunLoader();
        }

        switch ($resourceType) {
            case 'reference-entity-record':

                if (!$client instanceof AkeneoPimEnterpriseClientInterface) {
                    throw new LogicException(sprintf('%s is supported only in Enterprise Edition', $resourceType));
                }

                return new ReferenceEntityRecordLoader($loadProfile, $client);
        }

        return new UpsertableLoader($loadProfile, $client);
    }
}
