<?php

namespace AkeneoEtl\Tests\Unit\Infrastructure\Command;

use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Domain\Resource\Attribute;
use AkeneoEtl\Infrastructure\Command\ResourceComparer;
use PHPUnit\Framework\TestCase;

class ResourceComparerTest extends TestCase
{
    public function test_it_returns_a_comparison_table_for_2_resources()
    {
        $resource1 = Resource::fromArray([
            'identifier' => '0123456789',
            'family' => 'ziggy'
        ], 'product');
        $resource1->set(Attribute::create('description', 'web', 'en_GB'), 'Ziggy The Hydra');

        $resource2 = Resource::fromArray([
            'identifier' => '0123456789',
            'family' => 'pet'
        ], 'product');
        $resource2->set(Attribute::create('name', null, 'en_GB'), 'Ziggy');
        $resource2->set(Attribute::create('description', 'web', 'en_GB'), 'Ziggy The Pet');

        $comparer = new ResourceComparer();
        $table = $comparer->getCompareTable($resource1, $resource2);

        $this->assertEquals([
            ['0123456789', 'family',      'ziggy',           'pet'          ],
            ['0123456789', 'name',        '',                'Ziggy'        ],
            ['0123456789', 'description', 'Ziggy The Hydra', 'Ziggy The Pet'],
        ], $table);
    }

    public function test_it_returns_a_list_table_for_1_resource()
    {
        $resource = Resource::fromArray([
            'identifier' => '0123456789',
            'family' => 'pet'
        ], 'product');
        $resource->set(Attribute::create('name', null, 'en_GB'), 'Ziggy');
        $resource->set(Attribute::create('description', 'web', 'en_GB'), 'Ziggy The Pet');

        $comparer = new ResourceComparer();
        $table = $comparer->getCompareTable(null, $resource);

        $this->assertEquals([
            ['0123456789', 'family',      'pet'          ],
            ['0123456789', 'name',        'Ziggy'        ],
            ['0123456789', 'description', 'Ziggy The Pet'],
        ], $table);
    }
}
