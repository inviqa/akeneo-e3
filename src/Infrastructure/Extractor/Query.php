<?php

namespace AkeneoEtl\Infrastructure\Extractor;

use Akeneo\Pim\ApiClient\Search\SearchBuilder;
use AkeneoEtl\Domain\AkeneoSpecifics;
use AkeneoEtl\Domain\Profile\ExtractProfile;

class Query
{
    private array $data = [];

    public function __construct(ExtractProfile $profile, string $resourceType)
    {
        $builder = new SearchBuilder();

        foreach ($profile->getConditions() as $condition) {
            $builder->addFilter(
                $condition['field'],
                $condition['operator'],
                $condition['value'] ?? null
            );
        }

        $codeFieldName = AkeneoSpecifics::getCodeFieldName($resourceType);
        $builder->addFilter($codeFieldName, 'IN', $profile->getDryRunCodes());

        $this->data = ['search' => $builder->getFilters()];
    }

    public static function fromProfile(ExtractProfile $profile, string $resourceType): self
    {
        return new self($profile, $resourceType);
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
