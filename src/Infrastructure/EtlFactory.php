<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder;
use AkeneoE3\Application\ActionFactory;
use AkeneoE3\Application\SequentialTransformer;
use AkeneoE3\Domain\Extractor;
use AkeneoE3\Domain\Profile\ConnectionProfile;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\EtlProcess;
use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Profile\TransformProfile;
use AkeneoE3\Domain\Loader;
use AkeneoE3\Domain\Transformer;
use AkeneoE3\Infrastructure\Api\ApiSelector;
use AkeneoE3\Infrastructure\Extractor\ExtractorFactory;
use AkeneoE3\Infrastructure\Loader\LoaderFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class EtlFactory
{
    private array $clients = [];

    private ApiSelector $apiSelector;

    private ActionFactory $actionFactory;

    private EventDispatcherInterface $eventDispatcher;

    private LoaderFactory $loaderFactory;

    private ExtractorFactory $extractorFactory;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ActionFactory $actionFactory = null,
        ExtractorFactory $extractorFactory = null,
        LoaderFactory $loaderFactory = null
    ) {
        $this->eventDispatcher = $eventDispatcher;

        $this->apiSelector = new ApiSelector();
        $this->extractorFactory = $extractorFactory ?? new ExtractorFactory();
        $this->actionFactory = $actionFactory ?? new ActionFactory();
        $this->loaderFactory = $loaderFactory ?? new LoaderFactory();
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

        return $this->extractorFactory->create($resourceType, $extractProfile, $client);
    }

    public function createTransformer(TransformProfile $transformProfile): Transformer
    {
        $actions = $this->actionFactory->createActions($transformProfile);

        return new SequentialTransformer($actions);
    }

    public function createLoader(
        string $resourceType,
        ConnectionProfile $connectionProfile,
        LoadProfile $loadProfile
    ): Loader {
        $client = $this->getClient($connectionProfile);

        return $this->loaderFactory->createLoader($resourceType, $loadProfile, $client);
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
}
