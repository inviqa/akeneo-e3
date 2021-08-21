<?php

namespace AkeneoE3\Tests\Unit\Domain;

use AkeneoE3\Domain\AkeneoSpecifics;
use PHPUnit\Framework\TestCase;

class AkeneoSpecificsTest extends TestCase
{
    public function test_it_returns_code_field_name_by_resource_type()
    {
        $this->assertEquals('identifier', AkeneoSpecifics::getCodeFieldName('product'));
        $this->assertEquals('code', AkeneoSpecifics::getCodeFieldName('attribute'));
    }

    public function test_it_returns_channel_field_name_by_resource_type()
    {
        $this->assertEquals('scope', AkeneoSpecifics::getChannelFieldName('product'));
        $this->assertEquals('channel', AkeneoSpecifics::getChannelFieldName('reference-entity-record'));
    }
}
