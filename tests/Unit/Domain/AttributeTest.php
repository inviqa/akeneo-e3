<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Resource\Attribute;
use AkeneoEtl\Domain\Resource\Field;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    public function test_it_should_be_created()
    {
        $field = Attribute::create('colour', 'web', 'de_DE');

        Assert::assertInstanceOf(Field::class, $field);
        Assert::assertEquals('colour', $field->getName());
        Assert::assertEquals('web', $field->getScope());
        Assert::assertEquals('de_DE', $field->getLocale());
    }
}
