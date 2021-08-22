<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Loader\Api;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Domain\Load\LoadResult\LoadResult;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\ResourceCollection;
use AkeneoE3\Infrastructure\Loader\LoadListConnector;
use AkeneoE3\Infrastructure\Loader\LoadResultFactory;

class ReferenceEntityRecord implements LoadListConnector
{
    private AkeneoPimEnterpriseClientInterface $client;

    private LoadProfile $loadProfile;

    public function __construct(LoadProfile $loadProfile, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->loadProfile = $loadProfile;
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

        $entityCode = $resources->getFirst()->get(Property::create('reference_entity_code'));

        $api = $this->client->getReferenceEntityRecordApi();
        $response = $api->upsertList($entityCode, $resources->toArray($this->loadProfile->isDuplicateMode()));

        yield from LoadResultFactory::createFromResponse($response, $resources);
    }
}
