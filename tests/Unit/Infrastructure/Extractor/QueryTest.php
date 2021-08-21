<?php

namespace AkeneoE3\Tests\Unit\Infrastructure\Extractor;

use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Infrastructure\Extractor\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function test_it_can_be_created_from_load_profile_and_resource_type_product()
    {
        $query = Query::fromProfile(new FakeExtractProfile(
            ['123', 'abc'],
            [
                ['field' => 'family', 'operator' => 'IN', 'value' => ['pim']],
            ]
        ), 'product');

        $expected = [
            'search' => [
                'family'     => [['operator' => 'IN', 'value' => ['pim']]],
                'identifier' => [['operator' => 'IN', 'value' => ['123', 'abc']]],
            ]
        ];

        $this->assertEquals($expected, $query->toArray());
    }

    public function test_it_can_be_created_from_load_profile_and_resource_type_attribute()
    {
        $query = Query::fromProfile(new FakeExtractProfile(
            ['123', 'abc'],
            [
                ['field' => 'family', 'operator' => 'IN', 'value' => ['pim']],
            ]
        ), 'attribute');

        $expected = [
            'search' => [
                'family' => [['operator' => 'IN', 'value' => ['pim']]],
                'code'   => [['operator' => 'IN', 'value' => ['123', 'abc']]],
            ]
        ];

        $this->assertEquals($expected, $query->toArray());
    }
}

class FakeExtractProfile implements ExtractProfile
{
    private array $codes;

    private array $conditions;

    public function __construct(array $codes, array $conditions)
    {
        $this->codes = $codes;
        $this->conditions = $conditions;
    }

    public function getDryRunCodes(): array
    {
        return $this->codes;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }
}
