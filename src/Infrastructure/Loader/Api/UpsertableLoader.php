<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Loader\Api;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Infrastructure\Api\ApiSelector;
use LogicException;

class UpsertableLoader extends BaseBatchLoader
{
    private LoadProfile $profile;

    private AkeneoPimClientInterface $client;

    private ApiSelector $apiSelector;

    public function __construct(LoadProfile $loadProfile, AkeneoPimClientInterface $client)
    {
        $this->profile = $loadProfile;
        $this->client = $client;

        $this->apiSelector = new ApiSelector();

        parent::__construct($loadProfile);
    }

    /**
     * @param array|Resource[] $list
     */
    protected function upsertList(array $list): iterable
    {
        if (count($list) === 0) {
            return [];
        }

        $firstKey = array_key_first($list);
        $resourceType = $list[$firstKey]->getResourceType();

        $list = array_values($this->prepareBufferToUpsert($list));

        $api = $this->apiSelector->getApi($this->client, $resourceType);

        if (!$api instanceof UpsertableResourceListInterface) {
            throw new LogicException(sprintf('%s API does not support bulk upsert', $resourceType));
        }

        $result = $api->upsertList($list);

        return $result;
    }
}
