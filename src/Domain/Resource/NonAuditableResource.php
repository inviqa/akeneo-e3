<?php

declare(strict_types=1);

namespace AkeneoE3\Domain\Resource;

use Generator;
use LogicException;

/**
 * @internal
 */
final class NonAuditableResource implements Resource
{
    private PropertyValues $properties;

    private AttributeValues $attributes;

    private ResourceType $resourceType;

    private bool $isChanged = false;

    private string $code;

    private function __construct(array $data, ResourceType $resourceType)
    {
        $this->resourceType = $resourceType;

        $idFieldName = $resourceType->getCodeFieldName();
        if (array_key_exists($idFieldName, $data) === false) {
            throw new LogicException(sprintf('%s field is expected for %s resource type', $idFieldName, $resourceType));
        }

        $this->code = (string)$data[$idFieldName];
        $this->attributes = AttributeValues::fromArray($data['values'] ?? [], $resourceType);
        $this->properties = PropertyValues::fromArray($data);
    }

    public static function fromArray(array $data, ResourceType $resourceType): self
    {
        return new self($data, $resourceType);
    }

    public static function fromCode(string $code, ResourceType $resourceType): self
    {
        return new self([
            $resourceType->getCodeFieldName() => $code
        ], $resourceType);
    }

    public function getResourceType(): ResourceType
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

    public function removeFrom(Field $field, array $newValue): void
    {
        $this->isChanged = true;

        if ($field instanceof Property) {
            $this->properties->removeFrom($field, $newValue);
        }

        if ($field instanceof Attribute) {
            $this->attributes->removeFrom($field, $newValue);
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
        $codeField = Property::create($this->resourceType->getCodeFieldName());
        $this->properties->set($codeField, $code);
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

    public function toArray(bool $full): array
    {
        $data = $this->properties->toArray();

        if ($this->attributes->count() > 0) {
            $data['values'] = $this->attributes->toArray();
        }

        unset($data['group_labels']);

        return $data;
    }

    public function __clone()
    {
        $this->properties = clone $this->properties;
        $this->attributes = clone $this->attributes;
    }
}
