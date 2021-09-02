<?php

namespace AkeneoE3\Tests\Unit\Domain;

use AkeneoE3\Domain\Resource\Attribute;
use AkeneoE3\Domain\Resource\FieldFactory;
use AkeneoE3\Domain\Resource\NonAuditableResource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\ResourceType;
use LogicException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class NonAuditableResourceTest extends TestCase
{
    public function test_it_can_be_created_from_a_code()
    {
        $resource = NonAuditableResource::fromCode('123', ResourceType::create('product'));

        $this->assertEquals('123', $resource->getCode());
    }

    /**
     * @dataProvider getProviders
     */
    public function test_it_gets_values(array $options, $expectedValue)
    {
        $resource = NonAuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));
        $field = FieldFactory::fromOptions($options);
        $value = $resource->get($field);

        Assert::assertEquals($expectedValue, $value);
    }

    public function test_it_returns_true_if_a_field_exists()
    {
        $resource = NonAuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));
        $field = Property::create('family');

        $this->assertTrue($resource->has($field));
    }

    public function test_it_throws_an_exception_by_getting_a_property_that_does_not_exist()
    {
        $resource = NonAuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));
        $field = Property::create('reality');
        $this->expectException(LogicException::class);

        $resource->get($field);
    }

    public function test_it_throws_an_exception_by_getting_an_attribute_that_does_not_exist()
    {
        $resource = NonAuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));
        $field = Attribute::create('colour', 'reality', null);
        $this->expectException(LogicException::class);

        $resource->get($field);
    }

    public function test_it_returns_fields()
    {
        $product = [
            'identifier' => 'the-ziggy',
            'family' => 'ziggy',
            'values' => [
                'name' => [
                    ['scope' => 'web', 'locale' => 'en_GB', 'data' => 'Ziggy'],
                ],
            ]
        ];

        $resource = NonAuditableResource::fromArray($product, ResourceType::create('product'));

        $fields = iterator_to_array($resource->fields());

        $this->assertEquals('identifier', $fields[0]->getName());
        $this->assertEquals('family', $fields[1]->getName());

        $this->assertEquals('name', $fields[2]->getName());
        $this->assertEquals('web', $fields[2]->getScope());
        $this->assertEquals('en_GB', $fields[2]->getLocale());
    }

    public function getProviders(): array
    {
        return [
            'for a top-level field value' =>
                [
                    ['field' => 'family'],
                    'ziggy',
                ],

            'for a top-level array field value' =>
                [
                    ['field' => 'categories'],
                    ['hydra', 'pim'],
                ],

            'for an attribute value (not-scopable, not-localisable)' =>
                [
                    ['field' => 'head_count', 'locale' => null],
                    3,
                ],

            'for an attribute value (scopable, localisable)' =>
                [
                    ['field' => 'name', 'scope' => 'web', 'locale' => 'de_DE'],
                    'Süßer Ziggy',
                ],

            'for an attribute value (not-scopable, localisable)' =>
                [
                    [
                        'field' => 'description',
                        'scope' => null,
                        'locale' => 'en_GB',
                    ],
                    'Ziggy - the Hydra',
                ],

            'for an attribute value (scopable, not-localisable)' =>
                [
                    ['field' => 'colour', 'scope' => 'erp', 'locale' => null],
                    'violet',
                ],
        ];
    }

    public function test_it_normalises_to_array()
    {
        $resource = NonAuditableResource::fromArray([
            'code' => 'ziggy',
            'labels' => ['de_DE' => 'Die Ziggy'],
            'values' => [
                'name' => [['scope' => null, 'locale' => null, 'data' => 'Ziggy']]
            ],
        ], ResourceType::create('object'));

        $this->assertEquals([
            'code' => 'ziggy',
            'labels' => ['de_DE' => 'Die Ziggy'],
            'values' => [
                'name' => [['scope' => null, 'locale' => null, 'data' => 'Ziggy']]
            ]
        ], $resource->toArray(true));
    }


    public function test_it_normalises_to_array_without_special_fields()
    {
        $resource = NonAuditableResource::fromArray([
            'code' => 'ziggy',
            '__ignoreA' => 'A',
            '__ignoreB' => 'B',
            'values' => [
                'name' => [['scope' => null, 'locale' => null, 'data' => 'Ziggy']]
            ],
        ], ResourceType::create('object'));

        $this->assertEquals([
            'code' => 'ziggy',
            'values' => [
                'name' => [['scope' => null, 'locale' => null, 'data' => 'Ziggy']]
            ]
        ], $resource->toArray(true));
    }
}
