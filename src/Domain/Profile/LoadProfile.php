<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Profile;

class LoadProfile
{
    private bool $isDryRun;

    private function __construct(array $data)
    {
        $this->isDryRun = ($data['type'] ?? '') === 'dry-run';
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function isDryRun(): bool
    {
        return $this->isDryRun;
    }
}
