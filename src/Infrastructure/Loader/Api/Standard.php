<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Loader\Api;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Domain\Load\LoadResult\LoadResult;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\Resource\ResourceCollection;
use AkeneoE3\Infrastructure\Api\ApiSelector;
use AkeneoE3\Infrastructure\Loader\LoadListConnector;
use AkeneoE3\Infrastructure\Loader\LoadResultFactory;

final class Standard implements LoadListConnector
{
    private AkeneoPimEnterpriseClientInterface $client;

    private LoadProfile $profile;

    public function __construct(LoadProfile $profile, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->profile = $profile;
        $this->client = $client;
    }

    /**
     * @return LoadResult[]
     */
    public function load(ResourceCollection $resources): iterable
    {
        if ($resources->count() === 0) {
            return [];
        }

        $apiSelector = new ApiSelector();

        $api = $apiSelector->getApi($this->client, $resources->getResourceType());
        $response = $api->upsertList($resources->toArray($this->profile->isDuplicateMode()));

        yield from LoadResultFactory::createFromResponse($response, $resources);
    }
}
