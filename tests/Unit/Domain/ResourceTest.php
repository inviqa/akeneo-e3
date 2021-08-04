<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Field;
use AkeneoEtl\Domain\Resource;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{
    /**
     * @dataProvider valueProviders
     */
    public function test_it_retrieves_values(array $options, $default, $expectedValue)
    {
        $resource = Resource::fromArray($this->getProductData(), 'product');
        $value = $resource->get(Field::fromOptions($options), $default);

        Assert::assertEquals($expectedValue, $value);
    }

    /**
     * @dataProvider patchArrayProviders
     */
    public function test_it_generates_patches(array $options, $newValue, bool $isAttribute, array $expectedValueArray)
    {
        $resource = Resource::fromArray([], 'product');
        $field = Field::fromOptions($options);
        $data = $resource->makeValueArray($field, $newValue, $isAttribute);

        Assert::assertEquals($expectedValueArray, $data);
    }

    public function valueProviders(): array
    {
        return [
            'return a top-level field value' =>
            [
                [
                    'field' => 'family'
                ],
                null,
                'ziggy'
            ],

            'return a top-level array field value' =>
            [
                [
                    'field' => 'categories'
                ],
                null,
                ['hydra', 'pim']
            ],

            'return an attribute value (not-scopable, not-localisable)' =>
            [
                [
                    'field' => 'head_count'
                ],
                null,
                3
            ],

            'return an attribute value (scopable, localisable)' =>
            [
                [
                    'field' => 'name',
                    'locale' => 'de_DE',
                    'scope' => 'web',
                ],
                null,
                'Süßes Ziggy'
            ],

            'return an attribute value (not-scopable, localisable)' =>
            [
                [
                    'field' => 'description',
                    'locale' => 'en_GB',
                    'scope' => null,
                ],
                null,
                'Ziggy - the Hydra'
            ],

            'return an attribute value (scopable, not-localisable)' =>
            [
                [
                    'field' => 'colour',
                    'locale' => null,
                    'scope' => 'mobile',
                ],
                null,
                'violet'
            ],

            'return a default value for non-existing attribute' =>
            [
                [
                    'field' => 'no-color',
                ],
                'pink',
                'pink'
            ],
        ];
    }

    public function patchArrayProviders()
    {
        return
        [
            'creates a patch for a top-level field' =>
            [
                [
                    'field' => 'family'
                ],
                'ziggy-academic',
                false,
                [
                    'family' => 'ziggy-academic'
                ],
            ],

            'creates a patch for an attribute' =>
            [
                [
                    'field' => 'colour',
                    'locale' => 'en_GB',
                    'scope' => 'web',
                ],
                'pink',
                true,
                [
                    'values' => [
                        'colour' => [
                            [
                                'scope' => 'web',
                                'locale' => 'en_GB',
                                'data' => 'pink',
                            ]
                        ]
                    ]
                ],
            ],

            'make a patch for an attribute if no locale and scope provided' =>
            [
                [
                    'field' => 'colour',
                ],
                'pink',
                true,
                [
                    'values' => [
                        'colour' => [
                            [
                                'scope' => null,
                                'locale' => null,
                                'data' => 'pink',
                            ]
                        ]
                    ]
                ],
            ],
        ];
    }

    private function getProductData(): array
    {
        return [
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
    }
}
