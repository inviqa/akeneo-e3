<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Resource;

use AkeneoEtl\Domain\AkeneoSpecifics;
use Generator;
use LogicException;

/**
 * @internal
 */
final class NonAuditableResource implements Resource
{
    private PropertyValues $properties;

    private AttributeValues $attributes;

    private string $resourceType;

    private bool $isChanged = false;

    private string $code;

    private function __construct(array $data, string $resourceType)
    {
        $this->resourceType = $resourceType;

        $idFieldName = $this->getCodeFieldName();
        if (array_key_exists($idFieldName, $data) === false) {
            throw new LogicException(sprintf('%s field is expected for %s resource type', $idFieldName, $resourceType));
        }

        $this->code = (string)$data[$idFieldName];
        $this->attributes = AttributeValues::fromArray($data['values'] ?? [], $resourceType);

        unset($data['values']);
        $this->properties = PropertyValues::fromArray($data);
    }

    public static function fromArray(array $data, string $resourceType): self
    {
        return new self($data, $resourceType);
    }

    public static function fromCode(string $code, string $resourceType): self
    {
        return new self([
            AkeneoSpecifics::getCodeFieldName($resourceType) => $code
        ], $resourceType);
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    /**
     * @return mixed
     */
    public function get(Field $field)
    {
        if ($field instanceof Property) {
            return $this->properties->get($field);
        }

        if ($field instanceof Attribute) {
            return $this->attributes->get($field);
        }
    }

    /**
     * @param mixed $newValue
     */
    public function set(Field $field, $newValue): void
    {
        // @todo: check if new value and old value are different

        $this->isChanged = true;

        if ($field instanceof Property) {
            $this->properties->set($field, $newValue);
        }

        if ($field instanceof Attribute) {
            $this->attributes->set($field, $newValue);
        }
    }

    public function addTo(Field $field, array $newValue): void
    {
        $this->isChanged = true;

        if ($field instanceof Property) {
            $this->properties->addTo($field, $newValue);
        }

        if ($field instanceof Attribute) {
            $this->attributes->addTo($field, $newValue);
        }
    }

    public function has(Field $field): bool
    {
        if ($field instanceof Property) {
            return $this->properties->has($field);
        }

        if ($field instanceof Attribute) {
            return $this->attributes->has($field);
        }

        return false;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $codeField = Property::create($this->getCodeFieldName());
        $this->properties->set($codeField, $code);
    }

    public function getCodeFieldName(): string
    {
        return AkeneoSpecifics::getCodeFieldName($this->resourceType);
    }

    public function isChanged(): bool
    {
        return $this->isChanged;
    }

    /**
     * @return Generator|Field[]
     */
    public function fields(): Generator
    {
        foreach ($this->properties->fields() as $field) {
            yield $field;
        }

        foreach ($this->attributes->attributes() as $attribute) {
            yield $attribute;
        }
    }

    public function toArray(): array
    {
        $data = $this->properties->toArray();

        if ($this->attributes->count() > 0) {
            $data['values'] = $this->attributes->toArray();
        }

        return $data;
    }

    public function __clone()
    {
        $this->properties = clone $this->properties;
        $this->attributes = clone $this->attributes;
    }
}
