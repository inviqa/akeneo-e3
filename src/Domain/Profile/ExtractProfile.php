<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Profile;

class ExtractProfile
{
    private array $conditions;

    private function __construct(array $data)
    {
        $this->conditions = $data['conditions'] ?? [];
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }
}
