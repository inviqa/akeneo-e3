<?php

namespace AkeneoE3\Tests\Unit\Domain\Resource;

use AkeneoE3\Domain\Resource\ResourceType;
use PHPUnit\Framework\TestCase;

class ResourceTypeTest extends TestCase
{
    public function test_it_can_be_created_from_type_code()
    {
        $resourceType = ResourceType::create('product');

        $this->assertEquals('product', (string)$resourceType);
    }

    public function test_it_returns_code_field_name_by_resource_type()
    {
        $resourceType = ResourceType::create('product');
        $this->assertEquals('identifier', $resourceType->getCodeFieldName());

        $resourceType = ResourceType::create('attribute');
        $this->assertEquals('code', $resourceType->getCodeFieldName());
    }

    public function test_it_returns_channel_field_name_by_resource_type()
    {
        $resourceType = ResourceType::create('product');
        $this->assertEquals('scope', $resourceType->getChannelFieldName());

        $resourceType = ResourceType::create('reference-entity-record');
        $this->assertEquals('channel', $resourceType->getChannelFieldName());
    }
}
