<?php

namespace AkeneoEtl\Tests\Unit\Application;

use AkeneoEtl\Application\Action\ValueAccessor;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ValueAccessorTest extends TestCase
{
    /**
     * @dataProvider valueProviders
     */
    public function test_it_gets_values_from_data_by_option(array $dataItem, array $options, $default, $expectedValue)
    {
        $accessor = new ValueAccessor();
        $value = $accessor->getValueByOptions($dataItem, $options, $default);

        Assert::assertEquals($expectedValue, $value);
    }

    public function valueProviders(): array
    {
        $product = [
            'family' => 'ziggy',
            'categories' => ['hydra', 'pim'],
            'values' => [

                'name' => [
                    [
                        'locale' => 'en_GB',
                        'scope' => 'web',
                        'data' => 'Ziggy'
                    ],
                    [
                        'locale' => 'de_DE',
                        'scope' => 'web',
                        'data' => 'Süßes Ziggy'
                    ]
                ],

                'description' => [
                    [
                        'locale' => 'en_GB',
                        'scope' => null,
                        'data' => 'Ziggy - the Hydra'
                    ],
                    [
                        'locale' => 'de_DE',
                        'scope' => 'web',
                        'data' => 'Ziggy - die Hydra'
                    ]
                ],

                'colour' => [
                    [
                        'locale' => null,
                        'scope' => 'web',
                        'data' => 'violet and white'
                    ],
                    [
                        'locale' => null,
                        'scope' => 'mobile',
                        'data' => 'violet'
                    ]
                ],

                'head_count' => [
                    [
                        'locale' => null,
                        'scope' => null,
                        'data' => 3
                    ]
                ]
            ],
        ];

        return [

            // return a top-level field value
            [
                $product,
                [
                    'field' => 'family'
                ],
                null,
                'ziggy'
            ],

            // return a top-level array field value
            [
                $product,
                [
                    'field' => 'categories'
                ],
                null,
                ['hydra', 'pim']
            ],

            // returns an attribute value (not-scopable, not-localisable)
            [
                $product,
                [
                    'field' => 'head_count'
                ],
                null,
                3
            ],

            // returns an attribute value (scopable, localisable)
            [
                $product,
                [
                    'field' => 'name',
                    'locale' => 'de_DE',
                    'scope' => 'web',
                ],
                null,
                'Süßes Ziggy'
            ],

            // returns an attribute value (not-scopable, localisable)
            [
                $product,
                [
                    'field' => 'description',
                    'locale' => 'en_GB',
                    'scope' => null,
                ],
                null,
                'Ziggy - the Hydra'
            ],

            // returns an attribute value (scopable, not-localisable)
            [
                $product,
                [
                    'field' => 'colour',
                    'locale' => null,
                    'scope' => 'mobile',
                ],
                null,
                'violet'
            ],

            // returns a default value for non-existing attribute
            [
                $product,
                [
                    'field' => 'no-color',
                ],
                'pink',
                'pink'
            ],
        ];
    }
}
