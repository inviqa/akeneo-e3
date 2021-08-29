<?php

namespace AkeneoE3\Domain\Resource;

use Stringable;

class ResourceType implements Stringable
{
    public const ATTRIBUTE_CODE_FIELD = '__attribute_code';
    public const FAMILY_CODE_FIELD = '__family_code';
    public const REFERENCE_ENTITY_CODE_FIELD = '__reference_entity_code';
    public const REFERENCE_ENTITY_ATTRIBUTE_CODE_FIELD = '__reference_entity_attribute_code';

    public const ASSET_FAMILY_CODE_FIELD = '__asset_family_code';
    public const ASSET_ATTRIBUTE_CODE_FIELD = '__asset_attribute_code';
    public const ASSET_CODE_FIELD = '__asset_code';

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
        return in_array($this->code, ['reference-entity-record', 'asset']) ? 'channel' : 'scope';
    }
}
