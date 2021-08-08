<?php

namespace AkeneoEtl\Tests\Unit\Infrastructure\Command;

use AkeneoEtl\Domain\Resource;
use AkeneoEtl\Domain\Attribute;
use AkeneoEtl\Infrastructure\Command\ResourceNormaliser;
use PHPUnit\Framework\TestCase;

class ResourceNormaliserTest extends TestCase
{
    public function test_it_normalises_a_resource_to_array()
    {
        $resource = Resource::fromArray([
            'identifier' => '01234567890123456789',
            'family' => 'ziggy'
        ], 'product');
        $resource->set(Attribute::create('description', 'web', 'en_GB'), 'Ziggy The Hydra is here');

        $normaliser = new ResourceNormaliser();

        $this->assertEquals([
            'identifier: 01234567890123456789',
            'family: ziggy',
            'description (web,en_GB): Ziggy The Hydra is here',
        ], $normaliser->normalise($resource));
    }
}
