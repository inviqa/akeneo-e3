<?php

namespace AkeneoEtl\Domain;

class EtlLoadProfile
{
    private bool $isDryRun;

    public static function fromArray(array $data): self
    {
        $profile = new self();
        $profile->isDryRun = ($data['type'] ?? '') === 'dry-run';

        return $profile;
    }

    public function isDryRun(): bool
    {
        return $this->isDryRun;
    }
}
