<?php

namespace AkeneoE3\Domain\Profile;

interface ExtractProfile
{
    public function getConditions(): array;

    public function getDryRunCodes(): array;
}
