<?php

namespace AkeneoEtl\Domain;

class EtlLoadProfile
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
