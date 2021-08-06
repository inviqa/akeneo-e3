<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\FieldFactory;
use AkeneoEtl\Domain\Property;
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
        $resource = Resource::fromArray(TestData::getProduct(), 'product');
        $field = FieldFactory::fromOptions($options);
        $value = $resource->get($field, $default);

        Assert::assertEquals($expectedValue, $value);
    }

    public function test_it_should_be_changed_if_set_applied()
    {
        $resource = Resource::fromArray(TestData::getProduct(), 'product');
        $resource->set(Property::create('family'), 'ziggy-mama');

        Assert::assertTrue($resource->isChanged());
    }

    public function test_it_should_not_be_changed_if_set_not_applied()
    {
        $resource = Resource::fromArray(TestData::getProduct(), 'product');

        Assert::assertFalse($resource->isChanged());
    }

    public function valueProviders(): array
    {
        return [
            'for a top-level field value' =>
            [
                ['field' => 'family'],
                null,
                'ziggy'
            ],

            'for a top-level array field value' =>
            [
                ['field' => 'categories'],
                null,
                ['hydra', 'pim']
            ],

            'for an attribute value (not-scopable, not-localisable)' =>
            [
                ['field' => 'head_count', 'locale' => null],
                null,
                3
            ],

            'for an attribute value (scopable, localisable)' =>
            [
                ['field' => 'name', 'scope' => 'web', 'locale' => 'de_DE'],
                null,
                'Süßer Ziggy'
            ],

            'for an attribute value (not-scopable, localisable)' =>
            [
                ['field' => 'description', 'scope' => null, 'locale' => 'en_GB',],
                null,
                'Ziggy - the Hydra'
            ],

            'for an attribute value (scopable, not-localisable)' =>
            [
                ['field' => 'colour', 'scope' => 'erp', 'locale' => null],
                null,
                'violet'
            ],

            'for a default value for non-existing property' =>
            [
                ['field' => 'no-color'],
                'pink',
                'pink'
            ],
        ];
    }
}
