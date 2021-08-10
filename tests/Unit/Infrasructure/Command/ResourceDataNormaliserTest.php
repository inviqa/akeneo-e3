<?php

namespace AkeneoEtl\Tests\Unit\Infrastructure\Command;

use AkeneoEtl\Infrastructure\Command\ResourceDataNormaliser;
use PHPUnit\Framework\TestCase;

class ResourceDataNormaliserTest extends TestCase
{
    public function test_it_normalises_data()
    {
        $normaliser = new ResourceDataNormaliser();

        $value = ['a', 'b'];
        $expected = 'a, b';

        $this->assertEquals($expected, $normaliser->normalise($value));
    }
}
