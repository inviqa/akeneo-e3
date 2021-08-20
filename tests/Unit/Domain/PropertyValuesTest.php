<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Exception\TransformException;
use AkeneoEtl\Domain\Resource\Property;
use AkeneoEtl\Domain\Resource\PropertyValues;
use LogicException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class PropertyValuesTest extends TestCase
{
    public function test_it_can_be_created_from_array()
    {
        $collection = PropertyValues::fromArray(
            TestData::getProperties()
        );

        Assert::assertGreaterThan(0, $collection->count());
    }

    public function test_it_gets_a_value()
    {
        $collection = PropertyValues::fromArray(
            TestData::getProperties()
        );

        Assert::assertEquals(
            'ziggy',
            $collection->get(Property::create('family'))
        );
    }

    public function test_it_has_a_property()
    {
        $collection = PropertyValues::fromArray(TestData::getProperties());

        Assert::assertTrue($collection->has(Property::create('family')));
    }

    public function test_it_does_not_have_a_property()
    {
        $collection = PropertyValues::fromArray(TestData::getProperties());

        Assert::assertFalse($collection->has(Property::create('~no-property~')));
    }

    public function test_it_returns_properties()
    {
        $collection = PropertyValues::fromArray([
            'identifier' => 'the-ziggy',
            'family' => 'ziggy',
            'categories' => ['hydra', 'pim']
        ]);

        $properties = iterator_to_array($collection->fields());

        $this->assertEquals('identifier', $properties[0]->getName());
        $this->assertEquals('family', $properties[1]->getName());
        $this->assertEquals('categories', $properties[2]->getName());
    }

    /**
     * @dataProvider setDataProvider
     */
    public function test_it_sets_values(string $field, $setValue, $expected)
    {
        $collection = PropertyValues::fromArray(TestData::getProperties());

        $property = Property::create($field);
        $collection->set($property, $setValue);

        Assert::assertEquals($expected, $collection->get($property));
    }

    public function setDataProvider(): array
    {
        return [
            'setting a scalar value' => [
                'field'    => 'family',
                'setValue' => 'ziggy-mama',
                'expected' => 'ziggy-mama',
            ],

            'setting a value to a property that was not set' => [
                'field'    => 'parent',
                'setValue' => 'Akeneo',
                'expected' => 'Akeneo',
            ],

            'setting a plain array' => [
                'field'    => 'categories',
                'setValue' => ['pxm', 'akeneo'],
                'expected' => ['pxm', 'akeneo'],
            ],

            'setting labels (one level object)' => [
                'field'    => 'labels',
                'setValue' => ['uk_UA' => 'Зіггі'],
                'expected' => [
                    'en_GB' => 'The Ziggy',
                    'de_DE' => 'Die Ziggy',
                    'uk_UA' => 'Зіггі',
                ]
            ],

            'setting associations (multilevel object)' => [
                'field'    => 'associations',
                'setValue' => [
                    'FRIENDS' => [
                        'products' => ['peggi'],
                        'product_models' => [],
                        'groups' => ['meggi'],
                        '--one-more--' => ['oggi'],
                    ],
                    'NEW' => [
                        'products' => ['naggi', 'nüggi'],
                        'product_models' => [],
                        'groups' => [],
                    ],
                ],
                'expected' => [
                    'FRIENDS' => [
                        'products' => ['peggi'],
                        'product_models' => [],
                        'groups' => ['meggi'],
                        '--one-more--' => ['oggi'],
                    ],
                    'RELATIVES' => [
                        'products' => ['rueggi'],
                        'product_models' => [],
                        'groups' => [],
                    ],
                    'NEW' => [
                        'products' => ['naggi', 'nüggi'],
                        'product_models' => [],
                        'groups' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider addToDataProvider
     */
    public function test_it_adds_values(string $field, $addValue, $expected)
    {
        $collection = PropertyValues::fromArray(TestData::getProperties());

        $property = Property::create($field);
        $collection->addTo($property, $addValue);

        Assert::assertEquals($expected, $collection->get($property));
    }

    public function addToDataProvider(): array
    {
        return [
            'adding items to categories' => [
                'field'    => 'categories',
                'addValue' => ['akeneo', 'pim'],
                'expected' => ['hydra', 'pim', 'akeneo'],
            ],

            'adding a value to a property that was not set' => [
                'field'    => 'new_array',
                'addValue' => ['Akeneo'],
                'expected' => ['Akeneo'],
            ],

            'adding associations' => [
                'field'    => 'associations',
                'addValue' => [
                    'FRIENDS' => [
                        'products' => ['peggi'],
                        'product_models' => [],
                        'groups' => ['meggi'],
                        '--one-more--' => ['oggi'],
                    ],
                    'NEW' => [
                        'products' => ['naggi', 'nüggi'],
                    ],
                ],
                'expected' => [
                    'FRIENDS' => [
                        'products' => ['yoggi', 'shoggi', 'peggi'],
                        'product_models' => ['moggi'],
                        'groups' => ['greggi', 'meggi'],
                        '--one-more--' => ['oggi'],
                    ],
                    'RELATIVES' => [
                        'products' => ['rueggi'],
                        'product_models' => [],
                        'groups' => [],
                    ],
                    'NEW' => [
                        'products' => ['naggi', 'nüggi'],
                    ],
                ],
            ],
        ];
    }

    public function test_it_throws_an_exception_by_adding_items_to_non_arrays()
    {
        $collection = PropertyValues::fromArray(TestData::getProperties());

        $property = Property::create('family');

        $this->expectException(TransformException::class);

        $collection->addTo($property, ['x']);
    }

    /**
     * @dataProvider removeFromDataProvider
     */
    public function test_it_removes_values(string $field, $removeValue, $expected)
    {
        $collection = PropertyValues::fromArray(TestData::getProperties());

        $property = Property::create($field);
        $collection->removeFrom($property, $removeValue);

        Assert::assertEquals($expected, $collection->get($property));
    }

    public function removeFromDataProvider(): array
    {
        return [
            'removing items from categories' => [
                'field'    => 'categories',
                'removeValue' => ['hydra'],
                'expected' => ['pim'],
            ],

            'removing associations' => [
                'field' => 'associations',
                'removeValue' => [
                    'FRIENDS' => [
                        'products' => ['yoggi'],
                        'product_models' => [],
                        'groups' => ['greggi'],
                    ],
                    'RELATIVES' => [
                        'products' => ['rueggi', 'reggi'],
                        'product_models' => ['doggi'],
                        'groups' => [],
                    ],
                ],
                'expected' => [
                    'FRIENDS' => [
                        'products' => ['shoggi'],
                        'product_models' => ['moggi'],
                        'groups' => [],
                    ],
                    'RELATIVES' => [
                        'products' => [],
                        'product_models' => [],
                        'groups' => [],
                    ],
                ],
            ],
        ];
    }


    public function test_it_throws_an_exception_by_removing_items_to_non_arrays()
    {
        $collection = PropertyValues::fromArray(TestData::getProperties());

        $property = Property::create('family');

        $this->expectException(TransformException::class);

        $collection->removeFrom($property, ['x']);
    }
}
