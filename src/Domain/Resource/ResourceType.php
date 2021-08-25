<?php

namespace AkeneoE3\Domain\Resource;

use Stringable;

class ResourceType implements Stringable
{
    public const ATTRIBUTE_CODE_FIELD = 'attribute_code';
    public const FAMILY_CODE_FIELD = 'family_code';
    public const REFERENCE_ENTITY_CODE_FIELD = 'reference_entity_code';

    private string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public static function create(string $code): self
    {
        return new self($code);
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function getCodeFieldName(): string
    {
        return $this->code !== 'product' ? 'code' : 'identifier';
    }

    public function getChannelFieldName(): string
    {
        return $this->code === 'reference-entity-record' ? 'channel' : 'scope';
    }
}
