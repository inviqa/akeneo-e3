<?php

declare(strict_types=1);

namespace AkeneoE3\Domain;

final class AkeneoSpecifics
{
    public static function getCodeFieldName(string $resourceType): string
    {
        return $resourceType !== 'product' ? 'code' : 'identifier';
    }

    public static function getChannelFieldName(string $resourceType): string
    {
        return $resourceType === 'reference-entity-record' ? 'channel' : 'scope';
    }
}
