<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\AkeneoSpecifics;
use PHPUnit\Framework\TestCase;

class AkeneoSpecificsTest extends TestCase
{

    public function test_it_returns_code_field_name_by_resource_type()
    {
        $this->assertEquals('identifier', AkeneoSpecifics::getCodeFieldName('product'));
        $this->assertEquals('code', AkeneoSpecifics::getCodeFieldName('attribute'));
    }
}
