<?php

namespace AkeneoE3\Tests\Unit\Domain;

use AkeneoE3\Domain\Resource\Attribute;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\NonAuditableResource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\ResourceType;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{
    public function test_it_can_be_created_from_a_code_or_identifier()
    {
        $resource = Resource::fromCode('123', ResourceType::create('product'));

        $this->assertEquals('123', $resource->getCode());
    }

    public function test_it_accumulates_changes_and_origins_data()
    {
        $resource = Resource::fromArray(TestData::getProduct(), ResourceType::create('product'));
        $resource->set(Property::create('family'), 'ziggy-mama');
        $resource->set(Attribute::create('name', 'web', 'de_DE'), 'Die Mutter von Ziggy');

        $changes = $resource->changes();
        $this->assertEquals([
            'identifier' => 'the-ziggy',
            'family' => 'ziggy-mama',
            'values' => [
                'name' => [
                    ['scope' => 'web', 'locale' => 'de_DE', 'data' => 'Die Mutter von Ziggy'],
                ]
            ]
        ], $changes->toArray());

        $origins = $resource->origins();
        $this->assertEquals([
            'identifier' => 'the-ziggy',
            'family' => 'ziggy',
            'values' => [
                'name' => [
                    ['scope' => 'web', 'locale' => 'de_DE', 'data' => 'Süßer Ziggy'],
                ]
            ]
        ], $origins->toArray());
    }


    public function test_it_should_be_changed_if_set_applied()
    {
        $resource = Resource::fromArray(TestData::getProduct(), ResourceType::create('product'));
        $resource->set(Property::create('family'), 'ziggy-mama');

        Assert::assertTrue($resource->isChanged());
    }

    public function test_it_should_not_be_changed_if_set_not_applied()
    {
        $resource = Resource::fromArray(TestData::getProduct(), ResourceType::create('product'));

        Assert::assertFalse($resource->isChanged());
    }

    public function test_it_should_be_changed_if_add_to_applied()
    {
        $resource = Resource::fromArray(TestData::getProduct(), ResourceType::create('product'));
        $resource->addTo(Property::create('categories'), ['pxm']);

        Assert::assertTrue($resource->isChanged());
    }
}
