<?php

namespace AkeneoEtl\Infrastructure\Api;


use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use LogicException;

class ApiSelector
{
    /**
     * @return mixed
     */
    public function getApi(AkeneoPimClientInterface $client, string $dataType)
    {
        $supportedApis = [
            'association-type'   => function() use ($client) { return $client->getAssociationTypeApi(); },
            'category'           => function() use ($client) { return $client->getCategoryApi(); },
            'channel'            => function() use ($client) { return $client->getChannelApi(); },
            'currency'           => function() use ($client) { return $client->getCurrencyApi(); },
            'locale'             => function() use ($client) { return $client->getLocaleApi(); },

            'attribute'          => function() use ($client) { return $client->getAttributeApi(); },
            'attribute-group'    => function() use ($client) { return $client->getAttributeGroupApi(); },
            'attribute-option'   => function() use ($client) { return $client->getAttributeOptionApi(); },

            'product'            => function() use ($client) { return $client->getProductApi(); },
            'product-model'      => function() use ($client) { return $client->getProductModelApi(); },

            'family'             => function() use ($client) { return $client->getFamilyApi(); },
            'family-variant'     => function() use ($client) { return $client->getFamilyVariantApi(); },

            'measure-family'     => function() use ($client) { return $client->getMeasureFamilyApi(); },
            'measurement-family' => function() use ($client) { return $client->getMeasurementFamilyApi(); },
        ];

        if (isset($supportedApis[$dataType]) === false) {
            throw new LogicException(sprintf('Unsupported data type: %s. Please, select one of following types: %s.',
                $dataType, implode(', ', array_keys($supportedApis))));
        }

        return $supportedApis[$dataType]();
    }
}
