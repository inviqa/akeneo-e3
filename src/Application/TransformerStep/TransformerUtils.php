<?php

namespace App\Application\TransformerStep;

class TransformerUtils
{
    public static function getFieldValue(array $data, string $name, ?string $channel, ?string $locale): array
    {
        foreach ($data['values'][$name] as $attributeValue) {
            if ($attributeValue['scope'] === $channel &&
                $attributeValue['locale'] === $locale) {
                return $attributeValue;
            }
        }

        return [];
    }

    /**
     * @param mixed $data
     */
    public static function createFieldArray(string $field, $data, ?string $channel, ?string $locale): array
    {
        return [
            'values' => [
                $field => [
                    ['scope' => $channel, 'locale' => $locale, 'data' => $data]
                ]
            ]
        ];
    }

    public static function getAttributeValue(array $data, string $path): array
    {
        $pathItems = explode('.', $path);
        $attributeName = $pathItems[0];
        $channel = $pathItems[1] !==  '~' ? $pathItems[1] : null;
        $locale = $pathItems[2] !==  '~' ? $pathItems[2] : null;

        foreach ($data['values'][$attributeName] as $attributeValue) {
            if ($attributeValue['scope'] === $channel &&
                $attributeValue['locale'] === $locale) {
                return $attributeValue;
            }
        }

        return [];
    }

    /**
     * @param mixed $data
     */
    public static function createAttributeValues(string $path, $data): array
    {
        $pathItems = explode('.', $path);
        $attributeName = $pathItems[0];
        $channel = $pathItems[1] !==  '~' ? $pathItems[1] : null;
        $locale = $pathItems[2] !==  '~' ? $pathItems[2] : null;

        return [
            'values' => [
                $attributeName => [
                    ['scope' => $channel, 'locale' => $locale, 'data' => $data]
                ]
            ]
        ];
    }
}
