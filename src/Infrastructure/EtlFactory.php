<?php

namespace AkeneoEtl\Infrastructure;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\Pim\ApiClient\Search\SearchBuilder;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder;
use AkeneoEtl\Application\ActionFactory;
use AkeneoEtl\Application\SequentialTransformer;
use AkeneoEtl\Domain\Hook\ActionTraceHook;
use AkeneoEtl\Domain\Hook\Hooks;
use AkeneoEtl\Domain\Hook\LoaderErrorHook;
use AkeneoEtl\Domain\Profile\ConnectionProfile;
use AkeneoEtl\Domain\Profile\ExtractProfile;
use AkeneoEtl\Domain\Profile\LoadProfile;
use AkeneoEtl\Domain\EtlProcess;
use AkeneoEtl\Domain\Profile\EtlProfile;
use AkeneoEtl\Domain\Profile\TransformProfile;
use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Transformer;
use AkeneoEtl\Infrastructure\Api\ApiSelector;
use AkeneoEtl\Infrastructure\Extractor\Extractor;
use AkeneoEtl\Infrastructure\Loader\ApiLoader;
use AkeneoEtl\Infrastructure\Loader\DryRunLoader;

class EtlFactory
{
    private array $clients = [];

    private ApiSelector $apiSelector;

    private ActionFactory $actionFactory;

    public function __construct()
    {
        $this->apiSelector = new ApiSelector();
        $this->actionFactory = new ActionFactory();
    }

    public function createEtlProcess(
        string $dataType,
        ConnectionProfile $sourceConnectionProfile,
        ConnectionProfile $destinationConnectionProfile,
        EtlProfile $etlProfile,
        Hooks $hooks
    ): EtlProcess {
        $extractor = $this->createExtractor(
            $dataType,
            $sourceConnectionProfile,
            $etlProfile->getExtractProfile()
        );

        $transformer = $this->createTransformer(
            $etlProfile->getTransformProfile(),
            $hooks
        );

        $loader = $this->createLoader(
            $dataType,
            $destinationConnectionProfile,
            $etlProfile->getLoadProfile(),
            $hooks
        );

        return new EtlProcess($extractor, $transformer, $loader, $hooks);
    }

    public function createExtractor(
        string $dataType,
        ConnectionProfile $profile,
        ExtractProfile $extractProfile
    ): Extractor {
        $client = $this->getClient($profile);

        return new Extractor(
            $this->apiSelector->getApi($client, $dataType),
            $this->buildQuery($extractProfile->getConditions())
        );
    }

    public function createTransformer(TransformProfile $transformProfile, ActionTraceHook $traceHook): Transformer
    {
        $actions = $this->actionFactory->createActions($transformProfile, $traceHook);

        return new SequentialTransformer($actions);
    }

    public function createLoader(
        string $dataType,
        ConnectionProfile $connectionProfile,
        LoadProfile $loadProfile,
        LoaderErrorHook $onLoaderError
    ): Loader {
        if ($loadProfile->isDryRun() === true) {
            return new DryRunLoader();
        }

        $client = $this->getClient($connectionProfile);

        return new ApiLoader(
            $this->apiSelector->getApi($client, $dataType),
            $onLoaderError
        );
    }

    private function getClient(
        ConnectionProfile $profile
    ): AkeneoPimClientInterface {
        $profileKey = $profile->getHost().$profile->getUserName();

        if (isset($this->clients[$profileKey]) === false) {
            $this->clients[$profileKey] = $this->createClient($profile);
        }

        return $this->clients[$profileKey];
    }

    private function createClient(ConnectionProfile $profile): AkeneoPimClientInterface
    {
        $clientBuilder = new AkeneoPimEnterpriseClientBuilder($profile->getHost());

        return $clientBuilder->buildAuthenticatedByPassword(
            $profile->getClientId(),
            $profile->getClientSecret(),
            $profile->getUserName(),
            $profile->getUserPassword()
        );
    }

    private function buildQuery(array $conditions): array
    {
        $searchBuilder = new SearchBuilder();

        foreach ($conditions as $condition) {
            $searchBuilder
                ->addFilter(
                    $condition['field'],
                    $condition['operator'],
                    $condition['value']
                );
        }

        $searchFilters = $searchBuilder->getFilters();

        return ['search' => $searchFilters];
    }
}
