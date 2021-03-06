<?php

namespace AkeneoE3\Tests\Unit\Domain;

class TestData
{
    public static function getProduct(): array
    {
        return [
            'identifier' => 'the-ziggy',
            'family' => 'ziggy',
            'categories' => ['hydra', 'pim'],
            'values' => self::getValues(),
        ];
    }

    public static function getProperties(): array
    {
        return [
            'identifier' => 'the-ziggy',
            'family' => 'ziggy',
            'categories' => ['hydra', 'pim'],
            'labels' => [
                'en_GB' => 'The Ziggy',
                'de_DE' => 'Die Ziggy',
            ],
            'associations' => [
                'FRIENDS' => [
                    'products' => ['yoggi', 'shoggi'],
                    'product_models' => ['moggi'],
                    'groups' => ['greggi'],
                ],
                'RELATIVES' => [
                    'products' => ['rueggi'],
                    'product_models' => [],
                    'groups' => [],
                ],
            ],
        ];
    }

    public static function getValues(): array
    {
        return [
            'name' => [
                ['scope' => 'web', 'locale' => 'en_GB', 'data' => 'Ziggy'],
                ['scope' => 'web', 'locale' => 'de_DE', 'data' => 'Süßer Ziggy'],
            ],

            'description' => [
                ['scope' => null, 'locale' => 'en_GB', 'data' => 'Ziggy - the Hydra'],
                ['scope' => null, 'locale' => 'de_DE', 'data' => 'Ziggy - die Hydra'],
            ],

            'colour' => [
                ['scope' => 'web', 'locale' => null, 'data' => 'violet and white'],
                ['scope' => 'erp', 'locale' => null, 'data' => 'violet'],
            ],

            'head_count' => [
                ['scope' => null, 'locale' => null, 'data' => 3]
            ],
        ];
    }


    public static function getValuesWithArrayData(): array
    {
        return [
            'head_names' => [
                ['scope' => 'web', 'locale' => null, 'data' => ['Lyon', 'Paris']]
            ]
        ];
    }

    public static function getSimpleProduct(): array
    {
        return [
            'identifier' => 'the-ziggy',
            'family' => 'ziggy',
            'categories' => ['hydra', 'pim'],
            'labels' => [
                'en_GB' => 'The Ziggy',
                'de_DE' => 'Die Ziggy',
            ],
            'values' => [],
        ];
    }
}
