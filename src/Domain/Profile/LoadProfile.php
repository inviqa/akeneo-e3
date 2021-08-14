<?php

namespace AkeneoEtl\Domain\Profile;

interface LoadProfile
{
    public function isDryRun(): bool;

    public function getMode(): string;
}
