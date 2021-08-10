<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Resource\Field;
use AkeneoEtl\Domain\Resource\Property;
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
