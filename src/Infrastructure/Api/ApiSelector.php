<?php

namespace AkeneoEtl\Infrastructure\Api;


use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;

class ApiSelector
{
    /**
     * @return mixed
     */
    public function getApi(AkeneoPimClientInterface $client, string $dataType)
    {
        $supportedApis = [
            'product'       => function() use ($client) { return $client->getProductApi(); },
            'product-model' => function() use ($client) { return $client->getProductModelApi(); },
        ];

        return $supportedApis[$dataType]();
    }
}
