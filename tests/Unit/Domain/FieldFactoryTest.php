<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Attribute;
use AkeneoEtl\Domain\FieldFactory;
use AkeneoEtl\Domain\Property;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class FieldFactoryTest extends TestCase
{
    public function test_it_returns_an_attribute_if_scope_or_locale_specified()
    {
        $field = FieldFactory::fromOptions([
            'field' => 'colour',
            'scope' => 'web',
            'locale' => 'de_DE',
        ]);

        Assert::assertInstanceOf(Attribute::class, $field);
    }

    public function test_it_returns_a_field_if_neither_scope_nor_locale_specified()
    {
        $field = FieldFactory::fromOptions([
            'field' => 'colour',
        ]);

        Assert::assertInstanceOf(Property::class, $field);
    }
}
