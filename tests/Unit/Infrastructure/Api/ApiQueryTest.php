<?php

namespace AkeneoE3\Tests\Unit\Infrastructure\Api;

use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use LogicException;
use PHPUnit\Framework\TestCase;

class ApiQueryTest extends TestCase
{
    public function test_it_returns_search_filters()
    {
        $query = ApiQuery::fromProfile(new FakeExtractProfile(
            ['123', 'abc'],
            [
                ['field' => 'ignore', 'value' => null],
                ['field' => 'family', 'operator' => 'IN', 'value' => ['pim']],
            ]
        ), ResourceType::create('product'));

        $expected = [
            'search' => [
                'family'     => [['operator' => 'IN', 'value' => ['pim']]],
                'identifier' => [['operator' => 'IN', 'value' => ['123', 'abc']]],
            ]
        ];

        $this->assertEquals($expected, $query->getSearchFilters(['ignore']));
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

    public function test_it_throws_an_exception_by_getting_an_unknown_field()
    {
        $query = ApiQuery::fromProfile(new FakeExtractProfile(
            [],
            [
                ['field' => 'reference_entity_code', 'value' => 'toys'],
                ['field' => 'complete', 'operator' => '=', 'value' => 'true'],
            ]
        ), ResourceType::create('reference-entity-record'));

        $this->expectException(LogicException::class);

        $query->getValue('unknown');
    }

    public function test_it_returns_true_if_a_field_is_present_in_filters()
    {
        $query = ApiQuery::fromProfile(new FakeExtractProfile(
            [],
            [
                ['field' => 'reference_entity_code', 'value' => 'toys'],
                ['field' => 'complete', 'operator' => '=', 'value' => 'true'],
            ]
        ), ResourceType::create('reference-entity-record'));

        $this->assertTrue($query->hasValue('reference_entity_code'));
    }

    public function test_it_returns_false_if_a_field_is_not_present_in_filters()
    {
        $query = ApiQuery::fromProfile(new FakeExtractProfile(
            [],
            [
                ['field' => 'complete', 'operator' => '=', 'value' => 'true'],
            ]
        ), ResourceType::create('reference-entity-record'));

        $this->assertFalse($query->hasValue('reference_entity_code'));
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
