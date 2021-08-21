<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use LogicException;

final class ApiSelector
{
    /**
     * @return mixed
     */
    public function getApi(AkeneoPimClientInterface $client, string $dataType)
    {
        $supportedApis = [
            'association-type'   => $client->getAssociationTypeApi(),
            'category'           => $client->getCategoryApi(),
            'channel'            => $client->getChannelApi(),
            'currency'           => $client->getCurrencyApi(),
            'locale'             => $client->getLocaleApi(),

            'attribute'          => $client->getAttributeApi(),
            'attribute-group'    => $client->getAttributeGroupApi(),
            'attribute-option'   => $client->getAttributeOptionApi(),

            'product'            => $client->getProductApi(),
            'product-model'      => $client->getProductModelApi(),

            'family'             => $client->getFamilyApi(),
            'family-variant'     => $client->getFamilyVariantApi(),

            'measurement-family' => $client->getMeasurementFamilyApi(),
        ];

        if ($client instanceof AkeneoPimEnterpriseClientInterface) {
            $supportedApis += [
                'reference-entity'                  => $client->getReferenceEntityApi(),
                'reference-entity-attribute'        => $client->getReferenceEntityAttributeApi(),
                'reference-entity-attribute-option' => $client->getReferenceEntityAttributeOptionApi(),
                'reference-entity-record'           => $client->getReferenceEntityRecordApi(),
                'reference-entity-media-file'       => $client->getReferenceEntityMediaFileApi(),

                'asset'                             => $client->getAssetManagerApi(),
                'asset-attribute'                   => $client->getAssetAttributeApi(),
                'asset-attribute-option'            => $client->getAssetAttributeOptionApi(),
                'asset-family'                      => $client->getAssetFamilyApi(),
                'asset-media-file'                  => $client->getAssetMediaFileApi(),
            ];
        }

        if (isset($supportedApis[$dataType]) === false) {
            throw new LogicException(sprintf(
                'Unsupported data type: %s. Please, select one of following types: %s.',
                $dataType,
                implode(', ', array_keys($supportedApis))
            ));
        }

        return $supportedApis[$dataType];
    }
}
