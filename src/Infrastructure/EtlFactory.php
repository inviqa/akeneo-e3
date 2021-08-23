<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Application\ActionFactory;
use AkeneoE3\Domain\Extractor;
use AkeneoE3\Domain\IterableExtractor;
use AkeneoE3\Domain\IterableLoader;
use AkeneoE3\Domain\IterableTransformer;
use AkeneoE3\Domain\Profile\ConnectionProfile;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\EtlProcess;
use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Profile\TransformProfile;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Query\ApiQueryFactory;
use AkeneoE3\Infrastructure\Api\RepositoryFactory;

final class EtlFactory
{
    private array $clients = [];

    private ActionFactory $actionFactory;

    private RepositoryFactory $repositoryFactory;

    public function __construct(
        ActionFactory $actionFactory = null,
        RepositoryFactory $repositoryFactory = null
    ) {
        $this->actionFactory = $actionFactory ?? new ActionFactory();
        $this->repositoryFactory = $repositoryFactory ?? new RepositoryFactory();
    }

    public function createEtlProcess(
        ResourceType $resourceType,
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

        return new EtlProcess(
            $extractor,
            $transformer,
            $loader
        );
    }

    public function createExtractor(
        ResourceType $resourceType,
        ConnectionProfile $profile,
        ExtractProfile $extractProfile
    ): IterableExtractor {
        $client = $this->getClient($profile);

        $repository = $this->repositoryFactory->createReadRepository($resourceType, $extractProfile, $client);

        $queryFactory = new ApiQueryFactory();
        $query = $queryFactory->fromProfile($extractProfile, $resourceType);

        return new IterableExtractor($repository, $query);
    }

    public function createTransformer(TransformProfile $transformProfile): IterableTransformer
    {
        $actions = $this->actionFactory->createActions($transformProfile);

        return new IterableTransformer($actions);
    }

    public function createLoader(
        ResourceType $resourceType,
        ConnectionProfile $connectionProfile,
        LoadProfile $loadProfile
    ): IterableLoader {
        $client = $this->getClient($connectionProfile);

        return new IterableLoader(
            $this->repositoryFactory->createWriteRepository($resourceType, $loadProfile, $client),
            $loadProfile
        );
    }

    private function getClient(ConnectionProfile $profile): AkeneoPimEnterpriseClientInterface
    {
        $profileKey = $profile->getHost().$profile->getUserName();

        if (isset($this->clients[$profileKey]) === false) {
            $this->clients[$profileKey] = $this->createClient($profile);
        }

        return $this->clients[$profileKey];
    }

    private function createClient(
        ConnectionProfile $profile
    ): AkeneoPimEnterpriseClientInterface {
        $clientBuilder = new AkeneoPimEnterpriseClientBuilder(
            $profile->getHost()
        );

        return $clientBuilder->buildAuthenticatedByPassword(
            $profile->getClientId(),
            $profile->getClientSecret(),
            $profile->getUserName(),
            $profile->getUserPassword()
        );
    }
}
