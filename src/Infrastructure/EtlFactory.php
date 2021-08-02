<?php

namespace AkeneoEtl\Infrastructure;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder;
use AkeneoEtl\Domain\ConnectionProfile;
use AkeneoEtl\Domain\EtlLoadProfile;
use AkeneoEtl\Domain\EtlProcess;
use AkeneoEtl\Domain\EtlProfile;
use AkeneoEtl\Domain\EtlTransformProfile;
use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Transformer;
use AkeneoEtl\Infrastructure\Loader\ApiLoader;
use AkeneoEtl\Infrastructure\Loader\DryRunLoader;
use Closure;

class EtlFactory
{
    private array $clients = [];

    public function createEtlProcess(
        ConnectionProfile $sourceConnectionProfile,
        ConnectionProfile $destinationConnectionProfile,
        EtlProfile $etlProfile,
        Closure $errorCallback
    ): EtlProcess {

        $extractor = $this->createExtractor(
            $sourceConnectionProfile,
            $etlProfile->getExtractorQuery()
        );

        $transformer = $this->createTransformer(
            $etlProfile->getTransformProfile()
        );

        $loader = $this->createLoader(
            $destinationConnectionProfile,
            $etlProfile->getLoadProfile(),
            $errorCallback
        );

        return new EtlProcess($extractor, $transformer, $loader);
    }

    public function createExtractor(
        ConnectionProfile $profile,
        array $query
    ): Extractor {
        $client = $this->getClient($profile);

        return new Extractor(
            $client->getProductApi(), $this->buildQuery($query)
        );
    }

    public function createTransformer(EtlTransformProfile $transformProfile
    ): Transformer {
        return new Transformer($transformProfile->transformerSteps);
    }

    public function createLoader(
        ConnectionProfile $connectionProfile,
        EtlLoadProfile $loadProfile,
        Closure $errorCallback
    ): Loader {
        if ($loadProfile->isDryRun) {
            return new DryRunLoader();
        }

        $client = $this->getClient($connectionProfile);

        return new ApiLoader($client->getProductApi(), $errorCallback);
    }

    private function getClient(ConnectionProfile $profile
    ): AkeneoPimClientInterface {
        $profileKey = $profile->host;

        if (isset($this->clients[$profileKey]) === false) {
            $this->clients[$profileKey] = $this->createClient($profile);
        }

        return $this->clients[$profileKey];
    }

    private function createClient(ConnectionProfile $profile
    ): AkeneoPimClientInterface {
        $clientBuilder = new AkeneoPimEnterpriseClientBuilder($profile->host);

        return $clientBuilder->buildAuthenticatedByPassword(
            $profile->clientId,
            $profile->clientSecret,
            $profile->userName,
            $profile->userPassword
        );
    }

    private function buildQuery(array $query): array
    {
        $searchBuilder = new \Akeneo\Pim\ApiClient\Search\SearchBuilder();

        foreach ($query['filters'] ?? [] as $filter) {
            $searchBuilder
                ->addFilter(
                    $filter['property'],
                    $filter['operator'],
                    $filter['value'],
                    $filter['options']
                );
        }

        $searchFilters = $searchBuilder->getFilters();

        return ['search' => $searchFilters];
    }
}
