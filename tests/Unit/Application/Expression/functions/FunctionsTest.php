<?php

namespace AkeneoE3\Tests\Unit\Application\Expression\functions;

use AkeneoE3\Application\Expression\ActionState;
use AkeneoE3\Domain\Exception\TransformException;
use AkeneoE3\Domain\Resource\Attribute;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Tests\Unit\Domain\TestData;
use PHPUnit\Framework\TestCase;
use function AkeneoE3\Application\Expression\Functions\value;
use function AkeneoE3\Application\Expression\Functions\hasAttribute;
use function AkeneoE3\Application\Expression\Functions\hasProperty;
use function AkeneoE3\Application\Expression\Functions\replace;

require_once('src/Application/Expression/Functions/functions.php');

class FunctionsTest extends TestCase
{
    public function test_function_value_returns_a_value()
    {
        $resource = AuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));

        ActionState::$resource = $resource;

        $this->assertEquals('violet', value('colour', 'erp', null));
    }

    public function test_function_value_throws_an_exception_for_an_unknown_attribute()
    {
        $resource = AuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));

        ActionState::$resource = $resource;

        $this->expectException(TransformException::class);

        value('colour', 'warehouse', null);
    }

    public function test_function_value_returns_a_value_for_the_current_field()
    {
        $resource = AuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));

        ActionState::$resource = $resource;
        ActionState::$field = Attribute::create('colour', 'erp', null);

        $this->assertEquals('violet', value());
    }

    public function test_function_value_throws_an_exception_if_the_current_field_does_not_exist()
    {
        $resource = AuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));

        ActionState::$resource = $resource;
        ActionState::$field = Attribute::create('colour', 'erp', 'es_ES');

        $this->expectException(TransformException::class);

        value();
    }

    public function test_function_has_attribute_returns_true_if_attribute_exists()
    {
        $resource = AuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));

        ActionState::$resource = $resource;

        $this->assertTrue(hasAttribute('colour', 'erp', null));
    }

    public function test_function_has_attribute_returns_false_if_attribute_does_not_exists()
    {
        $resource = AuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));

        ActionState::$resource = $resource;

        $this->assertFalse(hasAttribute('colour', 'mobile', null));
    }

    public function test_function_has_attribute_throws_an_exception_if_called_without_arguments_and_a_current_field_is_not_an_attribute()
    {
        $resource = AuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));
        ActionState::$resource = $resource;
        ActionState::$field = Property::create('categories');

        $this->expectException(TransformException::class);

        hasAttribute();
    }

    public function test_function_has_property_returns_true_if_property_exists()
    {
        $resource = AuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));

        ActionState::$resource = $resource;

        $this->assertTrue(hasProperty('categories'));
    }

    public function test_function_has_property_returns_false_if_property_does_not_exists()
    {
        $resource = AuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));

        ActionState::$resource = $resource;

        $this->assertFalse(hasProperty('friends'));
    }

    public function test_function_has_property_throws_an_exception_if_called_without_arguments_and_a_current_field_is_not_an_property()
    {
        $resource = AuditableResource::fromArray(TestData::getProduct(), ResourceType::create('product'));
        ActionState::$resource = $resource;
        ActionState::$field = Attribute::create('colour', null, null);

        $this->expectException(TransformException::class);

        hasProperty();
    }

    public function test_function_replace_replaces_strings()
    {
        $this->assertEquals('Lorem\nipsum\ndolor\nsit\namet', replace('Lorem ipsum dolor sit amet', ' ', '\n'));
    }
}
