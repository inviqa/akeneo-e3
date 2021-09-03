<?php

declare(strict_types=1);

namespace AkeneoE3\Application\Action;

use LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DuplicateOptions
{
    private array $includeFieldNames;

    private array $excludeFieldNames;

    private function __construct(array $includeFieldNames, array $excludeFieldNames)
    {
        $this->includeFieldNames = $includeFieldNames;
        $this->excludeFieldNames = $excludeFieldNames;
    }

    public static function fromArray(array $array): self
    {
        $resolver = new OptionsResolver();

        $array = $resolver
            ->setDefault('include_fields', [])
            ->setDefault('exclude_fields', [])
            ->setAllowedTypes('include_fields', ['array'])
            ->setAllowedTypes('exclude_fields', ['array'])
            ->resolve($array);

        if (count($array['include_fields']) > 0 &&
            count($array['exclude_fields']) > 0) {
            throw new LogicException(
                'Only one of the options `include_fields` or `exclude_fields` is required for the set action.'
            );
        }

        return new self($array['include_fields'], $array['exclude_fields']);
    }

    public function getIncludeFieldNames(): array
    {
        return $this->includeFieldNames;
    }

    public function getExcludeFieldNames(): array
    {
        return $this->excludeFieldNames;
    }
}
