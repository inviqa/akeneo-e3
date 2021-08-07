<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Api;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClient;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use LogicException;

class ApiSelector
{
    /**
     * @return mixed
     */
    public function getApi(AkeneoPimClientInterface $client, string $dataType)
    {
        $supportedApis = [
            'association-type'   => function () use ($client) {
                return $client->getAssociationTypeApi();
            },
            'category'           => function () use ($client) {
                return $client->getCategoryApi();
            },
            'channel'            => function () use ($client) {
                return $client->getChannelApi();
            },
            'currency'           => function () use ($client) {
                return $client->getCurrencyApi();
            },
            'locale'             => function () use ($client) {
                return $client->getLocaleApi();
            },

            'attribute'          => function () use ($client) {
                return $client->getAttributeApi();
            },
            'attribute-group'    => function () use ($client) {
                return $client->getAttributeGroupApi();
            },
            'attribute-option'   => function () use ($client) {
                return $client->getAttributeOptionApi();
            },

            'product'            => function () use ($client) {
                return $client->getProductApi();
            },
            'product-model'      => function () use ($client) {
                return $client->getProductModelApi();
            },

            'family'             => function () use ($client) {
                return $client->getFamilyApi();
            },
            'family-variant'     => function () use ($client) {
                return $client->getFamilyVariantApi();
            },

            'measurement-family' => function () use ($client) {
                return $client->getMeasurementFamilyApi();
            },
        ];

        if ($client instanceof AkeneoPimEnterpriseClientInterface) {
            $supportedApis += [
                'reference-entity'                  => function () use ($client) {
                    return $client->getReferenceEntityApi();
                },
                'reference-entity-attribute'        => function () use ($client) {
                    return $client->getReferenceEntityAttributeApi();
                },
                'reference-entity-attribute-option' => function () use ($client) {
                    return $client->getReferenceEntityAttributeOptionApi();
                },
                'reference-entity-record'           => function () use ($client) {
                    return $client->getReferenceEntityRecordApi();
                },
                'reference-entity-media-file'       => function () use ($client) {
                    return $client->getReferenceEntityMediaFileApi();
                },

                'asset'                              => function () use ($client) {
                    return $client->getAssetManagerApi();
                },
                'asset-attribute'                    => function () use ($client) {
                    return $client->getAssetAttributeApi();
                },
                'asset-attribute-option'             => function () use ($client) {
                    return $client->getAssetAttributeOptionApi();
                },
                'asset-family'                       => function () use ($client) {
                    return $client->getAssetFamilyApi();
                },
                'asset-media-file'                   => function () use ($client) {
                    return $client->getAssetMediaFileApi();
                },
            ];
        }

        if (isset($supportedApis[$dataType]) === false) {
            throw new LogicException(sprintf(
                'Unsupported data type: %s. Please, select one of following types: %s.',
                $dataType,
                implode(', ', array_keys($supportedApis))
            ));
        }

        return $supportedApis[$dataType]();
    }
}
