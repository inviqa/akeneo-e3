<?php

namespace AkeneoEtl\Domain\Profile;

interface LoadProfile
{
    public function isDryRun(): bool;

    public function getUploadMode(): string;

    public function getDryRunCodes(): array;
}
