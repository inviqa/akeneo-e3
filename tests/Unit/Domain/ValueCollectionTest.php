<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Attribute;
use AkeneoEtl\Domain\Field;
use AkeneoEtl\Domain\ValueCollection;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ValueCollectionTest extends TestCase
{
    public function test_it_can_be_created_from_array()
    {
        $collection = ValueCollection::fromArray(
            $this->getValueArray()
        );

        Assert::assertGreaterThan(0, $collection->count());
    }

    public function test_it_gets_a_value()
    {
        $collection = ValueCollection::fromArray(
            $this->getValueArray()
        );

        Assert::assertEquals(
            3,
            $collection->get(Attribute::create('head_count', null, null)));
    }

    private function getValueArray(): array
    {
        return [
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
        ];
    }
}
