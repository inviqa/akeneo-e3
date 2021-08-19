<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\ArrayUtils;
use PHPUnit\Framework\TestCase;

class ArrayUtilsTest extends TestCase
{
    public function test_it_returns_true_if_an_array_is_like_a_json_object()
    {
        $data = [
            'a' => 'x',
            'b' => 'y',
        ];

        $this->assertTrue(ArrayUtils::isLikeObject($data));
    }

    public function test_it_returns_false_if_an_array_is_not_like_a_json_object()
    {
        $data = [
            0 => 'x',
            1 => 'y',
        ];

        $this->assertFalse(ArrayUtils::isLikeObject($data));
    }

    public function test_it_returns_true_if_value_is_scalar()
    {
        $this->assertTrue(ArrayUtils::isScalarOrSimpleArray(42.5));
        $this->assertTrue(ArrayUtils::isScalarOrSimpleArray('abc'));
        $this->assertTrue(ArrayUtils::isScalarOrSimpleArray(true));
    }

    public function test_it_returns_true_if_value_is_plain_object()
    {
        $this->assertTrue(ArrayUtils::isScalarOrSimpleArray([42.5, 86]));
        $this->assertTrue(ArrayUtils::isScalarOrSimpleArray([123, 'abc']));
    }

    public function test_it_returns_false_if_value_is_not_scalar_and_not_plain_object()
    {
        $this->assertFalse(ArrayUtils::isScalarOrSimpleArray(['a' => 42.5, 'b' => 86]));
    }

    public function test_it_returns_true_if_value_is_a_plain_object()
    {
        $this->assertTrue(ArrayUtils::isSimpleArray([42.5, 'b']));
    }

    public function test_it_returns_false_if_value_is_not_a_plain_object()
    {
        $this->assertFalse(ArrayUtils::isSimpleArray(['a' => 'b']));
        $this->assertFalse(ArrayUtils::isSimpleArray('a'));
        $this->assertFalse(ArrayUtils::isSimpleArray(123));
        $this->assertFalse(ArrayUtils::isSimpleArray(true));
    }
}
