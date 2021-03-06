<?php

namespace AkeneoE3\Tests\Unit\Domain;

use AkeneoE3\Domain\Resource\Attribute;
use AkeneoE3\Domain\Resource\AttributeValues;
use AkeneoE3\Domain\Resource\ResourceType;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AttributeValuesTest extends TestCase
{
    public function test_it_can_be_created_from_array()
    {
        $collection = AttributeValues::fromArray(
            TestData::getValues()
        );

        Assert::assertGreaterThan(0, $collection->count());
    }

    public function test_it_gets_a_value()
    {
        $collection = AttributeValues::fromArray(
            TestData::getValues()
        );

        Assert::assertEquals(
            3,
            $collection->get(Attribute::create('head_count', null, null))
        );
    }

    public function test_it_has_an_attribute()
    {
        $collection = AttributeValues::fromArray(
            TestData::getValues()
        );

        Assert::assertTrue(
            $collection->has(Attribute::create('head_count', null, null))
        );
    }

    public function test_it_does_not_have_an_attribute()
    {
        $collection = AttributeValues::fromArray(
            TestData::getValues()
        );

        Assert::assertFalse(
            $collection->has(Attribute::create('paw_count', null, null))
        );
    }

    public function test_it_sets_a_value_of_an_existing_attribute()
    {
        $collection = AttributeValues::fromArray(
            TestData::getValues()
        );

        $attribute = Attribute::create('head_count', null, null);
        $collection->set($attribute, 2);

        Assert::assertEquals(2, $collection->get($attribute));
    }

    public function test_it_sets_a_value_of_a_new_attribute()
    {
        $collection = AttributeValues::fromArray(
            TestData::getValues()
        );

        $attribute = Attribute::create('name', null, 'ua_UA');
        $collection->set($attribute, 'Зіггі');

        Assert::assertEquals('Зіггі', $collection->get($attribute));
    }

    public function test_it_adds_new_items_to_an_existing_attribute()
    {
        $collection = AttributeValues::fromArray(
            TestData::getValuesWithArrayData()
        );

        $attribute = Attribute::create('head_names', 'web', null);
        $collection->addTo($attribute, ['Nantes', 'Paris']);

        Assert::assertEquals(['Lyon', 'Paris', 'Nantes'], $collection->get($attribute));
    }

    public function test_it_removes_items_from_an_existing_attribute()
    {
        $collection = AttributeValues::fromArray(
            TestData::getValuesWithArrayData()
        );

        $attribute = Attribute::create('head_names', 'web', null);
        $collection->removeFrom($attribute, ['Paris']);

        Assert::assertEquals(['Lyon'], $collection->get($attribute));
    }

    public function test_it_does_not_change_value_by_removing_items_that_were_not_in_attribute_values()
    {
        $collection = AttributeValues::fromArray(
            TestData::getValuesWithArrayData()
        );

        $attribute = Attribute::create('head_names', 'web', null);
        $collection->removeFrom($attribute, ['London']);

        Assert::assertEquals(['Lyon', 'Paris'], $collection->get($attribute));
    }

    public function test_it_converts_to_array_with_scope_fields()
    {
        $collection = AttributeValues::fromArray([]);

        $collection->set(Attribute::create('a1', 's1', null), 1);
        $collection->set(Attribute::create('a1', 's2', null), 2);
        $collection->set(Attribute::create('a2', null, 'l1'), 3);

        $expected = [
            'a1' => [
                ['scope' => 's1', 'locale' => null, 'data' => 1],
                ['scope' => 's2', 'locale' => null, 'data' => 2],
            ],
            'a2' => [
                ['scope' => null, 'locale' => 'l1', 'data' => 3],
            ],
        ];

        $this->assertEquals($expected, $collection->toArray());
    }

    public function test_it_converts_to_array_with_channel_fields()
    {
        $collection = AttributeValues::fromArray([], ResourceType::create('reference-entity-record'));

        $collection->set(Attribute::create('a1', 's1', null), 1);
        $collection->set(Attribute::create('a1', 's2', null), 2);
        $collection->set(Attribute::create('a2', null, 'l1'), 3);

        $expected = [
            'a1' => [
                ['channel' => 's1', 'locale' => null, 'data' => 1],
                ['channel' => 's2', 'locale' => null, 'data' => 2],
            ],
            'a2' => [
                ['channel' => null, 'locale' => 'l1', 'data' => 3],
            ],
        ];

        $this->assertEquals($expected, $collection->toArray());
    }

    public function test_it_returns_attributes()
    {
        $collection = AttributeValues::fromArray(TestData::getValues());
        $attributes = iterator_to_array($collection->attributes());

        $this->assertEquals('name', $attributes[0]->getName());
        $this->assertEquals('web', $attributes[0]->getScope());
        $this->assertEquals('en_GB', $attributes[0]->getLocale());

        $this->assertEquals('description', $attributes[3]->getName());
        $this->assertEquals(null, $attributes[3]->getScope());
        $this->assertEquals('de_DE', $attributes[3]->getLocale());
    }
}
