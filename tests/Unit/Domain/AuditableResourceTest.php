<?php

namespace AkeneoE3\Tests\Unit\Domain;

use AkeneoE3\Domain\Resource\Attribute;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Property;
use PHPUnit\Framework\TestCase;

class AuditableResourceTest extends TestCase
{
    public function test_it_can_be_created_from_a_code_or_identifier()
    {
        $resource = AuditableResource::fromCode('123', 'product');

        $this->assertEquals('123', $resource->getCode());
    }

    public function test_it_accumulates_changes_and_origins_data()
    {
        $resource = AuditableResource::fromArray(TestData::getProduct(), 'product');
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
}
