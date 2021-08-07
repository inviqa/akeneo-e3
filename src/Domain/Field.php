<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

interface Field
{
    public function getName(): string;
}
