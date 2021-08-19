<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\ArrayHelper;
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
}
