<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Application\ActionFactory;
use AkeneoE3\Application\Expression\E3ExpressionEvaluator;
use AkeneoE3\Application\Expression\ExpressionObject;
use AkeneoE3\Domain\Extractor;
use AkeneoE3\Domain\Loader;
use AkeneoE3\Domain\Transformer;
use AkeneoE3\Domain\Profile\ConnectionProfile;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\EtlProcess;
use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Profile\TransformProfile;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\ExpressionObject\AkeneoObject;
use AkeneoE3\Infrastructure\Api\Query\ApiQueryFactory;
use AkeneoE3\Infrastructure\Api\RepositoryFactory;

final class EtlFactory
{
    private array $clients = [];

    private RepositoryFactory $repositoryFactory;

    public function __construct(
        RepositoryFactory $repositoryFactory = null
    ) {
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

        $expressionObject = new AkeneoObject($this->repositoryFactory, $this->getClient($sourceConnectionProfile));

        $transformer = $this->createTransformer(
            $etlProfile,
            $expressionObject
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
    ): Extractor {
        $client = $this->getClient($profile);

        $repository = $this->repositoryFactory->createReadRepository($resourceType, $client);

        $queryFactory = new ApiQueryFactory();
        $query = $queryFactory->fromProfile($extractProfile, $resourceType);

        return new Extractor($repository, $query);
    }

    public function createActionFactory(ExpressionObject $expressionObject): ActionFactory
    {
        $expressionLanguage = new E3ExpressionEvaluator($expressionObject);

        return new ActionFactory($expressionLanguage);
    }

    public function createTransformer(TransformProfile $transformProfile, ExpressionObject $expressionObject): Transformer
    {
        $actionFactory = $this->createActionFactory($expressionObject);

        $actions = $actionFactory->createActions($transformProfile);

        return new Transformer($actions);
    }

    public function createLoader(
        ResourceType $resourceType,
        ConnectionProfile $connectionProfile,
        LoadProfile $loadProfile
    ): Loader {
        $client = $this->getClient($connectionProfile);

        return new Loader(
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
