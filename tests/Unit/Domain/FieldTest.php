<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Field;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    public function test_it_should_be_created()
    {
        $field = Field::create('colour', [
            'scope' => 'web',
            'locale' => 'de_DE',
        ]);

        Assert::assertEquals('colour', $field->getName());
        Assert::assertEquals('web', $field->getScope());
        Assert::assertEquals('de_DE', $field->getLocale());
    }

    public function test_it_should_be_created_from_options()
    {
        $field = Field::fromOptions([
            'field' => 'colour',
            'scope' => 'web',
            'locale' => 'de_DE',
        ]);

        Assert::assertEquals('colour', $field->getName());
        Assert::assertEquals('web', $field->getScope());
        Assert::assertEquals('de_DE', $field->getLocale());
    }
}
