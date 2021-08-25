<?php

namespace AkeneoE3\Infrastructure\Api\Query;

use Akeneo\Pim\ApiClient\Search\SearchBuilder;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Repository\Query;
use AkeneoE3\Domain\Resource\ResourceType;
use LogicException;

class ApiQuery implements Query
{
    private array $indexedConditions;

    public function __construct(array $conditions = [])
    {
        $this->indexedConditions = $conditions;
    }

    public static function fromProfile(ExtractProfile $profile, ResourceType $resourceType): self
    {
        $conditions = [];

        foreach ($profile->getConditions() as $condition) {
            $fieldName = $condition['field'];
            $conditions[$fieldName] = $condition;
        }

        if (count($profile->getDryRunCodes()) > 0) {
            $codeFieldName = $resourceType->getCodeFieldName();
            $conditions[$codeFieldName] = ['operator' => 'IN', 'value' => $profile->getDryRunCodes()];
        }

        return new self($conditions);
    }

    public function getSearchFilters(array $excludedFieldNames): array
    {
        $builder = new SearchBuilder();

        foreach ($this->indexedConditions as $fieldName => $condition) {
            if (in_array($fieldName, $excludedFieldNames)) {
                continue;
            }

            $value = $condition['value'] ?? null;
            $operator = $condition['operator'] ?? '=';
            $builder->addFilter((string)$fieldName, $operator, $value);
        }

        return ['search' => $builder->getFilters()];
    }

    /**
     * @return mixed
     */
    public function getValue(string $field)
    {
        if ($this->hasValue($field) === false) {
            throw new LogicException(sprintf('The field %s is not defined in conditions', $field));
        }

        return $this->indexedConditions[$field]['value'];
    }

    public function hasValue(string $field): bool
    {
        return array_key_exists($field, $this->indexedConditions);
    }

    /**
     * @param mixed $value
     */
    public function addFilter(string $field, string $operator, $value): void
    {
        $this->indexedConditions[$field] = ['operator' => $operator, 'value' => $value];
    }
}
