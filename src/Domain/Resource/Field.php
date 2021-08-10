<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Resource;

interface Field
{
    public function getName(): string;
}
