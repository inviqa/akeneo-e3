<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Attribute;
use AkeneoEtl\Domain\Property;
use AkeneoEtl\Domain\Resource;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ResourceDiffTest extends TestCase
{
    public function test_it_generates_diff_for_scalar_properties()
    {
        $resource1 = Resource::fromArray(TestData::getSimpleProduct(), 'product');
        $resource2 = Resource::fromArray(TestData::getSimpleProduct(), 'product');

        $resource2->set(
            Property::create('family'),
            'unicorn'
        );

        $diff1 = $resource2->diff($resource1);
        $diff2 = $resource1->diff($resource2);

        Assert::assertEquals(
            ['family' => 'unicorn', 'identifier' => 'the-ziggy'],
            $diff1->toArray()
        );
        Assert::assertEquals(
            ['family' => 'ziggy', 'identifier' => 'the-ziggy'],
            $diff2->toArray()
        );
    }

    public function test_it_generates_diff_for_array_properties()
    {
        $resource1 = Resource::fromArray(TestData::getSimpleProduct(), 'product');
        $resource2 = Resource::fromArray(TestData::getSimpleProduct(), 'product');

        $resource2->set(
            Property::create('categories'),
            ['hydra', 'PXM']
        );

        $diff1 = $resource2->diff($resource1);
        $diff2 = $resource1->diff($resource2);

        Assert::assertEquals(
            ['categories' => ['hydra', 'PXM'], 'identifier' => 'the-ziggy'],
            $diff1->toArray()
        );
        Assert::assertEquals(
            ['categories' => ['hydra', 'pim'], 'identifier' => 'the-ziggy'],
            $diff2->toArray()
        );
    }

    public function test_it_generates_diff_for_object_properties()
    {
        $resource1 = Resource::fromArray(TestData::getSimpleProduct(), 'product');
        $resource2 = Resource::fromArray(TestData::getSimpleProduct(), 'product');

        $resource2->set(
            Property::create('labels'),
            [
                'en_GB' => 'The Ziggy',
                'de_DE' => 'Die Ziggy',
                'ua_UA' => 'Зіггі',
            ]
        );

        $diff1 = $resource2->diff($resource1);
        $diff2 = $resource1->diff($resource2);

        Assert::assertEquals(
            [
                'labels' => [
                    'en_GB' => 'The Ziggy',
                    'de_DE' => 'Die Ziggy',
                    'ua_UA' => 'Зіггі'
                ],
                'identifier' => 'the-ziggy'
            ],
            $diff1->toArray()
        );
        Assert::assertEquals(
            [
                'labels' => [
                    'en_GB' => 'The Ziggy',
                    'de_DE' => 'Die Ziggy',
                ],
                'identifier' => 'the-ziggy'
            ],
            $diff2->toArray()
        );
    }



    public function test_it_generates_diff_for_values()
    {
        $resource1 = Resource::fromArray(TestData::getProduct(), 'product');
        $resource2 = Resource::fromArray(TestData::getProduct(), 'product');

        $resource2->set(
            Attribute::create('name', 'web', 'ua_UA'),
            'Зіггі'
        );

        $diff1 = $resource2->diff($resource1);
        $diff2 = $resource1->diff($resource2);

        Assert::assertEquals(
            [
                'identifier' => 'the-ziggy',
                'values' => [
                    'name' => [
                        ['scope' => 'web', 'locale' => 'ua_UA', 'data' => 'Зіггі']
                    ]
                ]
            ],
            $diff1->toArray()
        );
        Assert::assertEquals(
            ['identifier' => 'the-ziggy'],
            $diff2->toArray()
        );
    }
}
