<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Resource;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ResourceMergeTest extends TestCase
{
    public function test_it_generates_merge_for_scalar_properties()
    {
        $resource1 = Resource::fromArray(TestData::getSimpleProduct(), 'product');
        $resource2 = Resource::fromArray([
            'identifier' => 'the-ziggy-mama',
            'parent' => 'akeneo',
        ], 'product');

        $merge = $resource1->merge($resource2);

        Assert::assertEquals([
            'identifier' => 'the-ziggy-mama',
            'family' => 'ziggy',
            'categories' => ['hydra', 'pim'],
            'labels' => [
                'en_GB' => 'The Ziggy',
                'de_DE' => 'Die Ziggy',
            ],
            'parent' => 'akeneo',
        ], $merge->toArray());
    }

    public function test_it_generates_merge_for_array_properties()
    {
        $resource1 = Resource::fromArray(TestData::getSimpleProduct(), 'product');
        $resource2 = Resource::fromArray([
            'identifier' => 'the-ziggy',
            'categories' => ['pxm'],
        ], 'product');

        $merge = $resource1->merge($resource2);

        Assert::assertEquals([
            'identifier' => 'the-ziggy',
            'family' => 'ziggy',
            'categories' => ['pxm'],
            'labels' => [
                'en_GB' => 'The Ziggy',
                'de_DE' => 'Die Ziggy',
            ],
        ], $merge->toArray());
    }

    public function test_it_generates_merge_for_object_properties()
    {
        $resource1 = Resource::fromArray(TestData::getSimpleProduct(), 'product');
        $resource2 = Resource::fromArray([
            'identifier' => 'the-ziggy',
            'labels' => [
                'ua_UA' => 'Зіггі',
            ],
        ], 'product');

        $merge = $resource1->merge($resource2);

        Assert::assertEquals([
            'identifier' => 'the-ziggy',
            'family' => 'ziggy',
            'categories' => ['hydra', 'pim'],
            'labels' => [
                'en_GB' => 'The Ziggy',
                'de_DE' => 'Die Ziggy',
                'ua_UA' => 'Зіггі',
            ],
        ], $merge->toArray());
    }

    public function test_it_generates_merge_for_values()
    {
        $resource1 = Resource::fromArray(TestData::getProduct(), 'product');
        $resource2 = Resource::fromArray([
            'identifier' => 'the-ziggy',
            'values' => [
                'name' => [
                    ['scope' => 'web', 'locale' => 'en_GB', 'data' => 'Ziggy The Best!'],
                ],
                'description' => [
                    ['scope' => null, 'locale' => 'ua_UA', 'data' => 'Гідра Зіггі'],
                ],
            ],
        ], 'product');

        $merge = $resource1->merge($resource2);

        Assert::assertEquals([
            'identifier' => 'the-ziggy',
            'family' => 'ziggy',
            'categories' => ['hydra', 'pim'],
            'values' => [
                'name' => [
                    ['scope' => 'web', 'locale' => 'en_GB', 'data' => 'Ziggy The Best!'],
                    ['scope' => 'web', 'locale' => 'de_DE', 'data' => 'Süßer Ziggy'],
                ],

                'description' => [
                    ['scope' => null, 'locale' => 'en_GB', 'data' => 'Ziggy - the Hydra'],
                    ['scope' => null, 'locale' => 'de_DE', 'data' => 'Ziggy - die Hydra'],
                    ['scope' => null, 'locale' => 'ua_UA', 'data' => 'Гідра Зіггі'],
                ],

                'colour' => [
                    ['scope' => 'web', 'locale' => null, 'data' => 'violet and white'],
                    ['scope' => 'erp', 'locale' => null, 'data' => 'violet'],
                ],

                'head_count' => [
                    ['scope' => null, 'locale' => null, 'data' => 3]
                ]

            ],
        ], $merge->toArray());
    }
}
