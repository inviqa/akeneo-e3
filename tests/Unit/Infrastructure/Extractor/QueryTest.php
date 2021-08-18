<?php

namespace AkeneoEtl\Tests\Unit\Infrastructure\Extractor;

use AkeneoEtl\Domain\Profile\ExtractProfile;
use AkeneoEtl\Infrastructure\Extractor\Query;
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
                ['field' => 'family', 'operator' => 'IN', 'value' => ['pim']],
                [
                    'field' => 'identifier',
                    'operator' => 'IN',
                    'value' => ['123', 'abc']
                ],
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
                ['field' => 'family', 'operator' => 'IN', 'value' => ['pim']],
                [
                    'field' => 'code',
                    'operator' => 'IN',
                    'value' => ['123', 'abc']
                ],
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
