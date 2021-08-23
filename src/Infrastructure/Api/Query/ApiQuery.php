<?php

namespace AkeneoE3\Infrastructure\Api\Query;

use Akeneo\Pim\ApiClient\Search\SearchBuilder;
use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Repository\Query;
use AkeneoE3\Domain\Resource\ResourceType;
use LogicException;

class ApiQuery implements Query
{
    private ExtractProfile $profile;

    private ResourceType $resourceType;

    private array $indexedConditions = [];

    public function __construct(ExtractProfile $profile, ResourceType $resourceType)
    {
        foreach ($profile->getConditions() as $condition) {
            $fieldName = $condition['field'];
            $this->indexedConditions[$fieldName] = $condition;
        }

        $this->profile = $profile;
        $this->resourceType = $resourceType;
    }

    public static function fromProfile(ExtractProfile $profile, ResourceType $resourceType): self
    {
        return new self($profile, $resourceType);
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

        if (count($this->profile->getDryRunCodes()) > 0) {
            $codeFieldName = $this->resourceType->getCodeFieldName();
            $builder->addFilter($codeFieldName, 'IN', $this->profile->getDryRunCodes());
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
}
