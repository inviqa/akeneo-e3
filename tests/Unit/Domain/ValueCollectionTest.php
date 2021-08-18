<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Resource\Attribute;
use AkeneoEtl\Domain\Resource\ValueCollection;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ValueCollectionTest extends TestCase
{
    public function test_it_can_be_created_from_array()
    {
        $collection = ValueCollection::fromArray(
            TestData::getValues()
        );

        Assert::assertGreaterThan(0, $collection->count());
    }

    public function test_it_gets_a_value()
    {
        $collection = ValueCollection::fromArray(
            TestData::getValues()
        );

        Assert::assertEquals(
            3,
            $collection->get(Attribute::create('head_count', null, null))
        );
    }

    public function test_it_has_an_attribute()
    {
        $collection = ValueCollection::fromArray(
            TestData::getValues()
        );

        Assert::assertTrue(
            $collection->has(Attribute::create('head_count', null, null))
        );
    }

    public function test_it_does_not_have_an_attribute()
    {
        $collection = ValueCollection::fromArray(
            TestData::getValues()
        );

        Assert::assertFalse(
            $collection->has(Attribute::create('paw_count', null, null))
        );
    }

    public function test_it_sets_a_value_of_an_existing_attribute()
    {
        $collection = ValueCollection::fromArray(
            TestData::getValues()
        );

        $attribute = Attribute::create('head_count', null, null);
        $collection->set($attribute, 2);

        Assert::assertEquals(2, $collection->get($attribute));
    }

    public function test_it_sets_a_value_of_a_new_attribute()
    {
        $collection = ValueCollection::fromArray(
            TestData::getValues()
        );

        $attribute = Attribute::create('name', null, 'ua_UA');
        $collection->set($attribute, 'Зіггі');

        Assert::assertEquals('Зіггі', $collection->get($attribute));
    }

    public function test_it_adds_new_items_to_an_existing_attribute()
    {
        $collection = ValueCollection::fromArray(
            TestData::getValuesWithArrayData()
        );

        $attribute = Attribute::create('head_names', 'web', null);
        $collection->addTo($attribute, ['Nantes', 'Paris']);

        Assert::assertEquals(['Lyon', 'Paris', 'Nantes'], $collection->get($attribute));
    }

    public function test_it_converts_to_array_with_scope_fields()
    {
        $collection = ValueCollection::fromArray([]);

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
        $collection = ValueCollection::fromArray([], 'reference-entity-record');

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

    public function test_it_returns_a_diff_with_another_collection()
    {
        $collection1 = ValueCollection::fromArray(TestData::getValues());

        $collection2 = ValueCollection::fromArray(TestData::getValues());
        $attribute = Attribute::create('head_count', null, null);
        $collection2->set($attribute, 2);
        $attribute = Attribute::create('name', 'web', 'de_DE');
        $collection2->set($attribute, 'Kleiner Ziggy');
        $attribute = Attribute::create('name', 'web', 'ua_UA');
        $collection2->set($attribute, 'Зіггі');
        $attribute = Attribute::create('mood', null, null);
        $collection2->set($attribute, 'fun');

        $diff = $collection2->diff($collection1);
        $expected = [
            'head_count' => [
                ['scope' => null, 'locale' => null, 'data' => 2],
            ],
            'name' => [
                ['scope' => 'web', 'locale' => 'de_DE', 'data' => 'Kleiner Ziggy'],
                ['scope' => 'web', 'locale' => 'ua_UA', 'data' => 'Зіггі'],
            ],
            'mood' => [
                ['scope' => null, 'locale' => null, 'data' => 'fun'],
            ],
        ];

        $this->assertEquals($expected, $diff->toArray());
    }


    public function test_it_returns_a_merge_with_another_collection()
    {
        $collection1 = ValueCollection::fromArray(TestData::getValues());

        $collection2 = ValueCollection::fromArray([]);
        $attribute = Attribute::create('head_count', null, null);
        $collection2->set($attribute, 2);
        $attribute = Attribute::create('name', 'web', 'ua_UA');
        $collection2->set($attribute, 'Зіггі');
        $attribute = Attribute::create('mood', null, null);
        $collection2->set($attribute, 'fun');

        $merge = $collection1->merge($collection2);
        $expected = [
            'name' => [
                ['scope' => 'web', 'locale' => 'en_GB', 'data' => 'Ziggy'],
                ['scope' => 'web', 'locale' => 'de_DE', 'data' => 'Süßer Ziggy'],
                ['scope' => 'web', 'locale' => 'ua_UA', 'data' => 'Зіггі'],
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
                ['scope' => null, 'locale' => null, 'data' => 2]
            ],
            'mood' => [
                ['scope' => null, 'locale' => null, 'data' => 'fun'],
            ],
        ];

        $this->assertEquals($expected, $merge->toArray());
    }

    public function test_it_returns_attributes()
    {
        $collection = ValueCollection::fromArray(TestData::getValues());
        $attributes = iterator_to_array($collection->attributes());

        $this->assertEquals('name', $attributes[0]->getName());
        $this->assertEquals('web', $attributes[0]->getScope());
        $this->assertEquals('en_GB', $attributes[0]->getLocale());

        $this->assertEquals('description', $attributes[3]->getName());
        $this->assertEquals(null, $attributes[3]->getScope());
        $this->assertEquals('de_DE', $attributes[3]->getLocale());
    }
}
