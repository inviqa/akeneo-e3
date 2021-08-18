<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

final class AkeneoSpecifics
{
    public static function getCodeFieldName(string $resourceType): string
    {
        return $resourceType !== 'product' ? 'code' : 'identifier';
    }
}
