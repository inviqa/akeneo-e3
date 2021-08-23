<?php

namespace AkeneoE3\Tests\Unit\Infrastructure\Api;

use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use LogicException;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function test_it_returns_search_filters()
    {
        $query = ApiQuery::fromProfile(new FakeExtractProfile(
            ['123', 'abc'],
            [
                ['field' => 'family', 'operator' => 'IN', 'value' => ['pim']],
            ]
        ), ResourceType::create('product'));

        $expected = [
            'search' => [
                'family'     => [['operator' => 'IN', 'value' => ['pim']]],
                'identifier' => [['operator' => 'IN', 'value' => ['123', 'abc']]],
            ]
        ];

        $this->assertEquals($expected, $query->getSearchFilters());
    }

    public function test_it_returns_filter_values_by_field_names()
    {
        $query = ApiQuery::fromProfile(new FakeExtractProfile(
            [],
            [
                ['field' => 'reference_entity_code', 'value' => 'toys'],
                ['field' => 'complete', 'operator' => '=', 'value' => 'true'],
            ]
        ), ResourceType::create('reference-entity-record'));


        $this->assertEquals('toys', $query->getValue('reference_entity_code'));
    }


    public function test_it_throws_an_exception_if_required_field_is_not_provided()
    {
        $this->expectException(LogicException::class);

        ApiQuery::fromProfile(new FakeExtractProfile(
            [],
            [
                ['field' => 'complete', 'operator' => '=', 'true'],
            ]
        ), ResourceType::create('reference-entity-record'));
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
