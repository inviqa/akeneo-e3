<?php

namespace AkeneoEtl\Domain\Profile;

interface ExtractProfile
{
    public function getConditions(): array;

    public function getDryRunCodes(): array;
}
