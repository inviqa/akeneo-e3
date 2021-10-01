<?php

namespace AkeneoE3\Tests\Unit\Infrastructure\Comparer;

use AkeneoE3\Infrastructure\Comparer\ResourceDataNormaliser;
use PHPUnit\Framework\TestCase;

class ResourceDataNormaliserTest extends TestCase
{
    /**
     * @dataProvider exampleProvider
     */
    public function test_it_normalises($value, string $expected)
    {
        $normaliser = new ResourceDataNormaliser();

        $this->assertEquals($expected, $normaliser->normalise($value));
    }

    public function exampleProvider()
    {
        return [
            'simple scalar value' => [
                'abc', 'abc'
            ],

            'array' => [
                ['a', 'b'], 'a, b'
            ],

            'price' => [
                ['amount' => 100, 'currency' => 'EUR'],
                <<<'END'
                 amount: 100
                 currency: EUR
                 END
            ],

            'object (labels)' => [
                [
                    'en_GB' => 'The Ziggy',
                    'de_DE' => 'Die Ziggy',
                ],
                <<<'END'
                 en_GB: The Ziggy
                 de_DE: Die Ziggy
                 END
            ],

            'object (associations)' => [
                [
                    'FRIENDS' => [
                        'products' => [123, 'abc', 'hey!'],
                        'product-models' => ['def', 456],
                    ],
                    'RELATIVES' => [
                        'groups' => ['xyz', 789],
                    ],
                ],
                <<<'END'
                 FRIENDS.products: 123, abc, hey!
                 FRIENDS.product-models: def, 456
                 RELATIVES.groups: xyz, 789
                 END
            ],
        ];
    }
}
