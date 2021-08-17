<?php

namespace AkeneoEtl\Tests\Unit\Infrastructure\Comparer;

use AkeneoEtl\Domain\Resource\Property;
use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Domain\Resource\Attribute;
use AkeneoEtl\Infrastructure\Comparer\DiffLine;
use AkeneoEtl\Infrastructure\Comparer\ResourceComparer;
use PHPUnit\Framework\TestCase;

class ResourceComparerTest extends TestCase
{
    public function test_it_returns_a_comparison_table()
    {
        $family = Property::create('family');
        $description = Attribute::create('description', 'web', 'en_GB');
        $name = Attribute::create('name', null, 'en_GB');

        $resource1 = Resource::fromArray([
                'identifier' => '0123456789',
                'family' => 'ziggy'
            ], 'product')
            ->set($description, 'Ziggy The Hydra');

        $resource2 = Resource::fromResource($resource1)
            ->set($family, 'pet')
            ->set($name, 'Ziggy')
            ->set($description, 'Ziggy The Pet');

        $comparer = new ResourceComparer();
        $table = $comparer->compareWithOrigin($resource2);

        $this->assertEquals([
            DiffLine::create('0123456789', $family, 'ziggy', 'pet'),
            DiffLine::create('0123456789', $description, 'Ziggy The Hydra', 'Ziggy The Pet'),
            DiffLine::create('0123456789', $name, '', 'Ziggy'),
        ], $table);
    }
}
