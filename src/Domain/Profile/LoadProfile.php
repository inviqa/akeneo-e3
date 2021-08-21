<?php

namespace AkeneoE3\Domain\Profile;

interface LoadProfile
{
    public function isDryRun(): bool;

    public function getUploadMode(): string;
}
