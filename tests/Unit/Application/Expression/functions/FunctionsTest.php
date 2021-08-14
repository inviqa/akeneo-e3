<?php

namespace AkeneoEtl\Tests\Unit\Application\Expression\functions;

use AkeneoEtl\Application\Expression\StateHolder;
use AkeneoEtl\Domain\Exception\TransformException;
use AkeneoEtl\Domain\Resource\Attribute;
use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Tests\Unit\Domain\TestData;
use PHPUnit\Framework\TestCase;
use function AkeneoEtl\Application\Expression\Functions\value;

require_once('src/Application/Expression/Functions/functions.php');

class FunctionsTest extends TestCase
{
    public function test_function_value_returns_a_value()
    {
        $resource = Resource::fromArray(TestData::getProduct(), 'product');

        StateHolder::$resource = $resource;

        $this->assertEquals('violet', value('colour', 'erp', null));
    }

    public function test_function_value_throws_an_exception_for_an_unknown_attribute()
    {
        $resource = Resource::fromArray(TestData::getProduct(), 'product');

        StateHolder::$resource = $resource;

        $this->expectException(TransformException::class);

        value('colour', 'warehouse', null);
    }

    public function test_function_value_returns_a_value_for_the_current_field()
    {
        $resource = Resource::fromArray(TestData::getProduct(), 'product');

        StateHolder::$resource = $resource;
        StateHolder::$field = Attribute::create('colour', 'erp', null);

        $this->assertEquals('violet', value());
    }


    public function test_function_value_throws_an_exception_if_the_current_field_does_not_exist()
    {
        $resource = Resource::fromArray(TestData::getProduct(), 'product');

        StateHolder::$resource = $resource;
        StateHolder::$field = Attribute::create('colour', 'erp', 'es_ES');

        $this->expectException(TransformException::class);

        value();
    }
}
