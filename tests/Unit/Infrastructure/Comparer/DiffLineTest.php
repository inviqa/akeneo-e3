<?php

namespace AkeneoE3\Tests\Unit\Infrastructure\Comparer;

use AkeneoE3\Domain\Resource\Attribute;
use AkeneoE3\Infrastructure\Comparer\DiffLine;
use PHPUnit\Framework\TestCase;

class DiffLineTest extends TestCase
{
    public function test_it_can_be_created()
    {
        $field = Attribute::create('name', null, null);
        $line = DiffLine::create('012345', $field, 'ziggy', 'Ziggy');

        $this->assertEquals('012345', $line->getCode());
        $this->assertEquals($field, $line->getField());
        $this->assertEquals('ziggy', $line->getBefore());
        $this->assertEquals('Ziggy', $line->getAfter());
    }
}
