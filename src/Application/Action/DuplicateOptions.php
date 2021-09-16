<?php

declare(strict_types=1);

namespace AkeneoE3\Application\Action;

use LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DuplicateOptions
{
    private array $includeFieldNames;

    private array $excludeFieldNames;

    private string $includeFieldNamesExpression;

    private string $excludeFieldNamesExpression;

    private function __construct(
        array $includeFieldNames,
        array $excludeFieldNames,
        string $includeFieldNamesExpression,
        string $excludeFieldNamesExpression
    ) {
        $this->includeFieldNames = $includeFieldNames;
        $this->excludeFieldNames = $excludeFieldNames;
        $this->includeFieldNamesExpression = $includeFieldNamesExpression;
        $this->excludeFieldNamesExpression = $excludeFieldNamesExpression;
    }

    public static function fromArray(array $array): self
    {
        $resolver = new OptionsResolver();

        $array = $resolver
            ->setDefault('include_fields', [])
            ->setDefault('exclude_fields', [])
            ->setDefault('include_fields_expression', '')
            ->setDefault('exclude_fields_expression', '')
            ->setAllowedTypes('include_fields', ['array'])
            ->setAllowedTypes('include_fields_expression', ['string', 'null'])
            ->setAllowedTypes('exclude_fields_expression', ['string', 'null'])
            ->setAllowedTypes('exclude_fields', ['array'])
            ->resolve($array);

        if (count($array['include_fields']) > 0 &&
            count($array['exclude_fields']) > 0) {
            throw new LogicException(
                'Only one of the options `include_fields` or `exclude_fields` is required for the set action.'
            );
        }

        return new self($array['include_fields'], $array['exclude_fields'], $array['include_fields_expression'], $array['exclude_fields_expression']);
    }

    public function getIncludeFieldNames(): array
    {
        return $this->includeFieldNames;
    }

    public function getExcludeFieldNames(): array
    {
        return $this->excludeFieldNames;
    }

    public function getIncludeFieldNamesExpression(): string
    {
        return $this->includeFieldNamesExpression;
    }

    public function getExcludeFieldNamesExpression(): string
    {
        return $this->excludeFieldNamesExpression;
    }
}
