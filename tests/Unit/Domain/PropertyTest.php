<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Field;
use AkeneoEtl\Domain\Property;
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
