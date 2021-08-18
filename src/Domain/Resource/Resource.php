<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Resource;

use Generator;
use LogicException;

final class Resource
{
    private array $properties;

    private ValueCollection $values;

    private string $resourceType;

    private bool $isChanged = false;

    private string $code;

    private ?Resource $origin;

    private function __construct(array $data, string $resourceType)
    {
        $this->resourceType = $resourceType;

        $idFieldName = $this->getCodeFieldName();
        if (array_key_exists($idFieldName, $data) === false) {
            throw new LogicException(sprintf('%s field is expected for %s resource type', $idFieldName, $resourceType));
        }

        $this->code = (string)$data[$idFieldName];
        $this->values = ValueCollection::fromArray($data['values'] ?? [], $resourceType);

        unset($data['values']);
        $this->properties = $data;

        $this->origin = null;
    }

    public static function fromArray(array $data, string $resourceType): self
    {
        return new self($data, $resourceType);
    }

    public static function fromResource(Resource $resource): self
    {
        $newResource = clone $resource;

        $newResource->origin = $resource;

        return $newResource;
    }

    public static function fromCode(string $code, string $resourceType): self
    {
        return new self([
            'identifier' => $code
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
        $fieldName = $field->getName();

        if ($field instanceof Property) {
            if (array_key_exists($fieldName, $this->properties) === false) {
                throw new LogicException(sprintf('Field %s is not present in data', $fieldName));
            }

            return $this->properties[$fieldName];
        }

        if (!$field instanceof Attribute) {
            throw new LogicException('Unsupported type of field');
        }

        return $this->values->get($field);
    }

    /**
     * @param mixed $newValue
     */
    public function set(Field $field, $newValue): self
    {
        // @todo: check if new value and old value are different

        $this->isChanged = true;

        if ($field instanceof Property) {
            $this->properties[$field->getName()] = $newValue;

            return $this;
        }

        if (!$field instanceof Attribute) {
            throw new LogicException('Unsupported type of field');
        }

        $this->values->set($field, $newValue);

        return $this;
    }

    public function has(Field $field): bool
    {
        $fieldName = $field->getName();

        if ($field instanceof Property) {
            return array_key_exists($fieldName, $this->properties);
        }

        if (!$field instanceof Attribute) {
            throw new LogicException('Unsupported type of field');
        }

        return $this->values->has($field);
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->properties[$this->getCodeFieldName()] = $code;

        return $this;
    }

    public function getCodeFieldName(): string
    {
        return $this->resourceType !== 'product' ? 'code' : 'identifier';
    }

    public function diff(Resource $resource): self
    {
        $propertiesDiff = [];
        foreach ($this->properties as $name => $value) {
            // @todo: process objects (assoc arrays) as per doc
            if (array_key_exists($name, $resource->properties) === false ||
                $value !== $resource->properties[$name]) {
                $propertiesDiff[$name] = $value;
            }
        }

        $identifierFieldName = $this->getCodeFieldName();
        if (array_key_exists($identifierFieldName, $propertiesDiff) === false) {
            $propertiesDiff[$identifierFieldName] = $this->code;
        }

        $diff = new self($propertiesDiff, $this->resourceType);
        $diff->values = $this->values->diff($resource->values);

        return $diff;
    }

    /**
     * Merge a resource.
     *
     * The `merge` operation apart from `diff` is not important
     * for business logic and needed only to emulate
     * Akeneo's merge behaviour in tests.
     */
    public function merge(Resource $resource): self
    {
        $propertiesMerge = $this->properties;
        foreach ($resource->properties as $name => $value) {
            if ($name === 'associations') {
                // process  associations differently
                continue;
            }

            if (is_array($value) === true && $this->isObjectLikeArray($value) === true) {
                $value = array_merge($this->properties[$name], $value);
            }

            $propertiesMerge[$name] = $value;
        }

        foreach ($resource->properties['associations'] ?? [] as $associationType => $association) {
            foreach ($association as $associationResourceType => $identifiers) {
                $propertiesMerge['associations'][$associationType][$associationResourceType] = $identifiers;
            }
        }

        $merge = new self($propertiesMerge, $this->resourceType);
        $merge->values = $this->values->merge($resource->values);

        return $merge;
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
        foreach (array_keys($this->properties) as $propertyName) {
            yield Property::create($propertyName);
        }

        foreach ($this->values->attributes() as $attribute) {
            yield $attribute;
        }
    }

    public function getOrigin(): ?Resource
    {
        return $this->origin;
    }

    public function toArray(): array
    {
        $data = array_merge(
            [$this->getCodeFieldName() => $this->code],
            $this->properties
        );

        if ($this->values->count() > 0) {
            $data['values'] = $this->values->toArray();
        }

        return $data;
    }

    private function isObjectLikeArray(array $data): bool
    {
        return is_string(array_key_first($data));
    }

    public function __clone()
    {
        $this->values = clone $this->values;
    }
}
