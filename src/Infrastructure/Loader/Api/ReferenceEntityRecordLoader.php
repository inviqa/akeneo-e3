<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Loader\Api;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoEtl\Domain\Profile\LoadProfile;
use AkeneoEtl\Domain\Resource\Property;
use AkeneoEtl\Domain\Resource\Resource;

class ReferenceEntityRecordLoader extends BaseBatchLoader
{
    private LoadProfile $profile;

    private AkeneoPimEnterpriseClientInterface $client;

    public function __construct(LoadProfile $loadProfile, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->profile = $loadProfile;
        $this->client = $client;

        parent::__construct($loadProfile);
    }

    /**
     * @param array|Resource[] $list
     *
     * @return array
     */
    protected function upsertList(array $list): iterable
    {
        if (count($list) === 0) {
            return [];
        }

        $firstKey = array_key_first($list);
        $entityCode = $list[$firstKey]->get(Property::create('reference_entity_code'));

        $list = array_values($this->prepareBufferToUpsert($list));

        $api = $this->client->getReferenceEntityRecordApi();
        $result = $api->upsertList($entityCode, $list);

        return $result;
    }
}
