<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\Pim\ApiClient\Search\SearchBuilder;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder;
use AkeneoEtl\Application\ActionFactory;
use AkeneoEtl\Application\SequentialTransformer;
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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class EtlFactory
{
    private array $clients = [];

    private ApiSelector $apiSelector;

    private ActionFactory $actionFactory;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher, ActionFactory $actionFactory = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->apiSelector = new ApiSelector();
        $this->actionFactory = $actionFactory ?? new ActionFactory($eventDispatcher);
    }

    public function createEtlProcess(
        string $resourceType,
        ConnectionProfile $sourceConnectionProfile,
        ConnectionProfile $destinationConnectionProfile,
        EtlProfile $etlProfile
    ): EtlProcess {
        $extractor = $this->createExtractor(
            $resourceType,
            $sourceConnectionProfile,
            $etlProfile
        );

        $transformer = $this->createTransformer(
            $etlProfile
        );

        $loader = $this->createLoader(
            $resourceType,
            $destinationConnectionProfile,
            $etlProfile
        );

        return new EtlProcess($extractor, $transformer, $loader, $this->eventDispatcher);
    }

    public function createExtractor(
        string $resourceType,
        ConnectionProfile $profile,
        ExtractProfile $extractProfile
    ): Extractor {
        $client = $this->getClient($profile);

        return new Extractor(
            $resourceType,
            $this->apiSelector->getApi($client, $resourceType),
            $this->buildQuery($extractProfile->getConditions())
        );
    }

    public function createTransformer(TransformProfile $transformProfile): Transformer
    {
        $actions = $this->actionFactory->createActions($transformProfile);

        return new SequentialTransformer($actions);
    }

    public function createLoader(
        string $dataType,
        ConnectionProfile $connectionProfile,
        LoadProfile $loadProfile
    ): Loader {
        if ($loadProfile->isDryRun() === true) {
            return new DryRunLoader();
        }

        $client = $this->getClient($connectionProfile);

        return new ApiLoader($loadProfile, $client);
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
