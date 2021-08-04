<?php

namespace AkeneoEtl\Tests\Unit\Application\Action;

use AkeneoEtl\Application\Action\SetOptions;
use LogicException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class SetOptionsTest extends TestCase
{
    public function test_it_can_be_created_from_array_with_a_value()
    {
        $options = SetOptions::fromArray(
            [
                'field' => 'head_count',
                'locale' => 'de_DE',
                'scope' => null,
                'value' => 3,
            ]
        );

        Assert::assertEquals('head_count', $options->getFieldName());
        Assert::assertEquals('de_DE', $options->getLocale());
        Assert::assertEquals(null, $options->getScope());
        Assert::assertEquals(3, $options->getValue());
    }

    public function test_it_can_be_created_from_array_with_an_expression()
    {
        $options = SetOptions::fromArray(
            [
                'field' => 'head_count',
                'expression' => 'trim("")',
            ]
        );

        Assert::assertEquals('trim("")', $options->getExpression());
    }

    /**
     * @dataProvider invalidOptions
     */
    public function test_it_fails_on_creation_if_options_are_not_valid(array $options)
    {
        $this->expectException(LogicException::class);

        SetOptions::fromArray($options);
    }

    public function invalidOptions()
    {
        return [
            'field is not set' => [
                ['locale' => 'de_DE', 'scope' => null, 'value' => 3]
            ],

            'value or expression must be set' => [
                ['field' => 'head_count']
            ],

            'expression is not a string' => [
                ['field' => 'head_count', 'expression' => []]
            ]
        ];
    }
}
