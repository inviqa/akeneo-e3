<?php

namespace AkeneoE3\Tests\Unit\Domain;

use AkeneoE3\Domain\Resource\Field;
use AkeneoE3\Domain\Resource\Property;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    public function test_it_should_be_created()
    {
        $field = Property::create('colour');

        Assert::assertInstanceOf(Field::class, $field);
        Assert::assertEquals('colour', $field->getName());
    }
}
