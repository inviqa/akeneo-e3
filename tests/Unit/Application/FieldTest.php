<?php

namespace AkeneoEtl\Tests\Unit\Application;

use AkeneoEtl\Application\Action\Field;
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
    }
}
