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
     * @dataProvider changeProviders
     */
    public function test_it_returns_changes(array $options, $newValue, bool $isAttribute, array $expectedValueArray)
    {
        $resource = Resource::fromArray($this->getProductData(), 'product');
        $field = Field::fromOptions($options);

        $resource->set(Field::fromOptions($options), $newValue, $isAttribute);

        $data = $resource->changes()->toArray();

        Assert::assertEquals($expectedValueArray, $data);
    }

    public function test_it_should_be_changed_if_set_applied()
    {
        $resource = Resource::fromArray($this->getProductData(), 'product');
        $resource->set(Field::create('family', []), 'ziggy-mama', false);

        Assert::assertTrue($resource->isChanged());
    }

    public function test_it_should_not_be_changed_if_set_not_applied()
    {
        $resource = Resource::fromArray($this->getProductData(), 'product');

        Assert::assertFalse($resource->isChanged());
    }

    public function valueProviders(): array
    {
        return [
            'for a top-level field value' =>
            [
                [
                    'field' => 'family'
                ],
                null,
                'ziggy'
            ],

            'for a top-level array field value' =>
            [
                [
                    'field' => 'categories'
                ],
                null,
                ['hydra', 'pim']
            ],

            'for an attribute value (not-scopable, not-localisable)' =>
            [
                [
                    'field' => 'head_count'
                ],
                null,
                3
            ],

            'for an attribute value (scopable, localisable)' =>
            [
                [
                    'field' => 'name',
                    'locale' => 'de_DE',
                    'scope' => 'web',
                ],
                null,
                'Süßes Ziggy'
            ],

            'for an attribute value (not-scopable, localisable)' =>
            [
                [
                    'field' => 'description',
                    'locale' => 'en_GB',
                    'scope' => null,
                ],
                null,
                'Ziggy - the Hydra'
            ],

            'for an attribute value (scopable, not-localisable)' =>
            [
                [
                    'field' => 'colour',
                    'locale' => null,
                    'scope' => 'mobile',
                ],
                null,
                'violet'
            ],

            'for a default value for non-existing attribute' =>
            [
                [
                    'field' => 'no-color',
                ],
                'pink',
                'pink'
            ],
        ];
    }

    public function changeProviders()
    {
        return
        [
            'for a top-level field' =>
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

            'for an attribute' =>
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

            'for an attribute if no locale and scope provided' =>
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
