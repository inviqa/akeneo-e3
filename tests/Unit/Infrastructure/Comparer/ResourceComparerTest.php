<?php

namespace AkeneoE3\Tests\Unit\Infrastructure\Comparer;

use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Attribute;
use AkeneoE3\Infrastructure\Comparer\DiffLine;
use AkeneoE3\Infrastructure\Comparer\ResourceComparer;
use PHPUnit\Framework\TestCase;

class ResourceComparerTest extends TestCase
{
    public function test_it_returns_a_comparison_table()
    {
        $family = Property::create('family');
        $description = Attribute::create('description', 'web', 'en_GB');
        $name = Attribute::create('name', null, 'en_GB');

        $resource1 = AuditableResource::fromArray([
                'identifier' => '0123456789',
                'family' => 'ziggy'
            ], 'product');
        $resource1->set($description, 'Ziggy The Hydra');

        $resource2 = clone $resource1;

        $resource2->set($family, 'pet');
        $resource2->set($name, 'Ziggy');
        $resource2->set($description, 'Ziggy The Pet');

        $comparer = new ResourceComparer();
        $table = $comparer->compareWithOrigin($resource2);

        $this->assertEquals([
            DiffLine::create('0123456789', $family, 'ziggy', 'pet'),
            DiffLine::create('0123456789', $description, 'Ziggy The Hydra', 'Ziggy The Pet'),
            DiffLine::create('0123456789', $name, '', 'Ziggy'),
        ], $table);
    }
}
