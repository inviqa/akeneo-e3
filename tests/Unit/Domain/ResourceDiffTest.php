<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Attribute;
use AkeneoEtl\Domain\Field;
use AkeneoEtl\Domain\Property;
use AkeneoEtl\Domain\Resource;
use AkeneoEtl\Domain\StandardFormat\StandardFormatDiff;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Rogervila\ArrayDiffMultidimensional;

class ResourceDiffTest extends TestCase
{
    public function test_it_generates_diffs_for_properties()
    {
        $resource1 = Resource::fromArray($this->getBasicProductData(), 'product');
        $resource2 = Resource::fromArray($this->getBasicProductData(), 'product');

        $resource2->set(
            Property::create('family'),
            'unicorn'
        );

        $diff1 = $resource2->diff($resource1);
        $diff2 = $resource1->diff($resource2);

        Assert::assertEquals(
            ['family' => 'unicorn', 'identifier' => 'the-ziggy'],
            $diff1->toArray()
        );
        Assert::assertEquals(
            ['family' => 'ziggy', 'identifier' => 'the-ziggy'],
            $diff2->toArray()
        );
    }

    public function test_it_generates_diffs_for_array_properties()
    {
        $resource1 = Resource::fromArray($this->getBasicProductData(), 'product');
        $resource2 = Resource::fromArray($this->getBasicProductData(), 'product');

        $resource2->set(
            Property::create('categories'),
            ['hydra', 'PXM']
        );

        $diff1 = $resource2->diff($resource1);
        $diff2 = $resource1->diff($resource2);

        Assert::assertEquals(
            ['categories' => ['hydra', 'PXM'], 'identifier' => 'the-ziggy'],
            $diff1->toArray()
        );
        Assert::assertEquals(
            ['categories' => ['hydra', 'pim'], 'identifier' => 'the-ziggy'],
            $diff2->toArray()
        );
    }

    public function test_it_generates_diffs_for_object_properties()
    {
        $resource1 = Resource::fromArray($this->getBasicProductData(), 'product');
        $resource2 = Resource::fromArray($this->getBasicProductData(), 'product');

        $resource2->set(
            Property::create('labels'),
            [
                'en_GB' => 'The Ziggy',
                'de_DE' => 'Die Ziggy',
                'ua_UA' => 'Зіггі',
            ]
        );

        $diff1 = $resource2->diff($resource1);
        $diff2 = $resource1->diff($resource2);

        Assert::assertEquals(
            [
                'labels' => [
                    'en_GB' => 'The Ziggy',
                    'de_DE' => 'Die Ziggy',
                    'ua_UA' => 'Зіггі'
                ],
                'identifier' => 'the-ziggy'
            ],
            $diff1->toArray()
        );
        Assert::assertEquals(
            [
                'labels' => [
                    'en_GB' => 'The Ziggy',
                    'de_DE' => 'Die Ziggy',
                ],
                'identifier' => 'the-ziggy'
            ],
            $diff2->toArray()
        );
    }



    public function test_it_generates_diffs_for_values()
    {
        $resource1 = Resource::fromArray($this->getProductData(), 'product');
        $resource2 = Resource::fromArray($this->getProductData(), 'product');

        $resource2->set(
            Attribute::create('name', 'web', 'ua_UA'),
            'Зіггі'
        );

        $diff1 = $resource2->diff($resource1);
        $diff2 = $resource1->diff($resource2);

        Assert::assertEquals(
            [
                'identifier' => 'the-ziggy',
                'values' => [
                    'name' => [
                        [
                            'locale' => 'ua_UA',
                            'scope' => 'web',
                            'data' => 'Зіггі',
                        ]
                    ]
                ]
            ],
            $diff1->toArray()
        );
        Assert::assertEquals(
            ['identifier' => 'the-ziggy'],
            $diff2->toArray()
        );
    }


    private function getBasicProductData(): array
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

    private function getProductData(): array
    {
        return [
            'identifier' => 'the-ziggy',
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
