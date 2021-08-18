<?php

namespace AkeneoEtl\Infrastructure\Extractor;

use AkeneoEtl\Domain\AkeneoSpecifics;
use AkeneoEtl\Domain\Profile\ExtractProfile;

class Query
{
    private ExtractProfile $profile;

    private string $resourceType;

    public function __construct(ExtractProfile $profile, string $resourceType)
    {
        $this->profile = $profile;
        $this->resourceType = $resourceType;
    }

    public static function fromProfile(ExtractProfile $profile, string $resourceType): self
    {
        return new self($profile, $resourceType);
    }

    public function toArray(): array
    {
        return [
            'search' => array_merge($this->profile->getConditions(), [[
                'field' => AkeneoSpecifics::getCodeFieldName($this->resourceType),
                'operator' => 'IN',
                'value' => $this->profile->getDryRunCodes()
            ]])
        ];
    }
}
