<?php

namespace AkeneoE3\Tests\Unit\Domain\UpdateBehavior;

use AkeneoE3\Domain\UpdateBehavior\ArrayHelper;
use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{
    private ArrayHelper $arrayHelper;

    protected function setUp(): void
    {
        $this->arrayHelper = new ArrayHelper();
    }

    public function test_it_returns_true_if_an_array_is_like_a_json_object()
    {
        $data = [
            'a' => 'x',
            'b' => 'y',
        ];

        $this->assertTrue($this->arrayHelper->isLikeObject($data));
    }

    public function test_it_returns_false_if_an_array_is_not_like_a_json_object()
    {
        $data = [
            0 => 'x',
            1 => 'y',
        ];

        $this->assertFalse($this->arrayHelper->isLikeObject($data));
    }

    public function test_it_returns_true_if_value_is_scalar()
    {
        $this->assertTrue($this->arrayHelper->isScalarOrSimpleArray(42.5));
        $this->assertTrue($this->arrayHelper->isScalarOrSimpleArray('abc'));
        $this->assertTrue($this->arrayHelper->isScalarOrSimpleArray(true));
    }

    public function test_it_returns_true_if_value_is_plain_object()
    {
        $this->assertTrue($this->arrayHelper->isScalarOrSimpleArray([42.5, 86]));
        $this->assertTrue($this->arrayHelper->isScalarOrSimpleArray([123, 'abc']));
    }

    public function test_it_returns_false_if_value_is_not_scalar_and_not_plain_object()
    {
        $this->assertFalse($this->arrayHelper->isScalarOrSimpleArray(['a' => 42.5, 'b' => 86]));
    }

    public function test_it_returns_true_if_value_is_a_plain_object()
    {
        $this->assertTrue($this->arrayHelper->isSimpleArray([42.5, 'b']));
    }

    public function test_it_returns_false_if_value_is_not_a_plain_object()
    {
        $this->assertFalse($this->arrayHelper->isSimpleArray(['a' => 'b']));
        $this->assertFalse($this->arrayHelper->isSimpleArray('a'));
        $this->assertFalse($this->arrayHelper->isSimpleArray(123));
        $this->assertFalse($this->arrayHelper->isSimpleArray(true));
    }

    public function test_it_returns_true_if_value_is_a_plain_array_or_a_json_like_object()
    {
        $this->assertTrue($this->arrayHelper->isSimpleArrayOrLikeObject([42.5, 'b']));
        $this->assertTrue($this->arrayHelper->isSimpleArrayOrLikeObject(['a' => 42.5, 'b' => 'abc']));
    }

    public function test_it_returns_false_if_value_is_not_a_plain_array_and_not_a_json_like_object()
    {
        $this->assertFalse($this->arrayHelper->isSimpleArrayOrLikeObject('abc'));
        $this->assertFalse($this->arrayHelper->isSimpleArrayOrLikeObject(42.5));
        $this->assertFalse($this->arrayHelper->isSimpleArrayOrLikeObject(true));
        $this->assertFalse($this->arrayHelper->isSimpleArrayOrLikeObject(null));
    }

    public function test_it_returns_true_if_types_match()
    {
        $this->assertTrue($this->arrayHelper->haveMatchingTypes([42.5], ['a']));
        $this->assertTrue($this->arrayHelper->haveMatchingTypes(['a' => 42.5], ['c' => 'ddd']));
        $this->assertTrue($this->arrayHelper->haveMatchingTypes(null, 333));
        $this->assertTrue($this->arrayHelper->haveMatchingTypes('null?', true));
    }

    public function test_it_returns_false_if_types_do_not_match()
    {
        $this->assertFalse($this->arrayHelper->haveMatchingTypes([42.5], ['a' => 1]));
        $this->assertFalse($this->arrayHelper->haveMatchingTypes(['a' => 42.5], 'abc'));
        $this->assertFalse($this->arrayHelper->haveMatchingTypes(null, [42.5]));
        $this->assertFalse($this->arrayHelper->haveMatchingTypes(null, ['a' => 42.5]));
        $this->assertFalse($this->arrayHelper->haveMatchingTypes(888, ['a' => 42.5]));
    }

    public function test_it_merges()
    {
        $this->assertEquals(
            [42.5, 'b', 'c'],
            $this->arrayHelper->merge([42.5, 'b'], ['b', 'c'])
        );
    }


    public function test_it_subtracts()
    {
        $this->assertEquals(
            [1],
            $this->arrayHelper->subtract([1, 2, 3], [2, 3])
        );

        $this->assertEquals(
            [42.5],
            $this->arrayHelper->subtract([42.5, 'b'], ['b', 'c'])
        );
    }
}
