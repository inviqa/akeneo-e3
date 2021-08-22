<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Loader\Api;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Domain\Load\LoadResult\Failed;
use AkeneoE3\Domain\Load\LoadResult\Loaded;
use AkeneoE3\Domain\Load\LoadResult\LoadResult;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Infrastructure\Loader\LoadConnector;
use Exception;

class ReferenceEntity implements LoadConnector
{
    private LoadProfile $profile;

    private AkeneoPimEnterpriseClientInterface $client;

    public function __construct(LoadProfile $loadProfile, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->client = $client;
        $this->profile = $loadProfile;
    }

    public function load(Resource $resource): LoadResult
    {
        $api = $this->client->getReferenceEntityApi();

        $entityCode = $resource->getCode();

        try {
            $api->upsert($entityCode, $resource->toArray($this->profile->isDuplicateMode()));

            return Loaded::create($resource);
        } catch (Exception $e) {
            return Failed::create($resource, $e->getMessage());
        }
    }
}
