<?php

namespace App\Infrastructure;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder;
use App\Domain\ConnectionProfile;
use App\Domain\EtlLoadProfile;
use App\Domain\EtlProcess;
use App\Domain\EtlProfile;
use App\Domain\EtlTransformProfile;
use App\Domain\Loader;
use App\Domain\Transformer;
use App\Infrastructure\Loader\ApiLoader;
use App\Infrastructure\Loader\DryRunLoader;
use Closure;

class EtlFactory
{
    private array $clients = [];

    public function createEtlProcess(
        ConnectionProfile $connectionProfile,
        EtlProfile $etlProfile,
        Closure $errorCallback
    ): EtlProcess {

        $extractor = $this->createExtractor(
            $connectionProfile,
            $etlProfile->getExtractorQuery()
        );

        $transformer = $this->createTransformer(
            $etlProfile->getTransformProfile()
        );

        $loader = $this->createLoader(
            $connectionProfile,
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
        callable $errorCallback
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
