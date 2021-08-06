<?php

namespace AkeneoEtl\Domain;

use LogicException;

class Resource
{
    private array $properties;

    private ValueCollection $values;

    private string $resourceType;

    private bool $isChanged = false;

    private string $codeOrIdentifier;

    private function __construct(array $data, string $resourceType)
    {
        $this->resourceType = $resourceType;

        $idFieldName = $this->getCodeOrIdentifierFieldName();
        if (array_key_exists($idFieldName, $data) === false) {
            throw new LogicException(sprintf('%s field is expected for %s resource type', $idFieldName, $resourceType));
        }

        $this->codeOrIdentifier = $data[$idFieldName];
        $this->values = ValueCollection::fromArray($data['values'] ?? []);

        unset($data['values']);
        $this->properties = $data;
    }

    public static function fromArray(array $data, string $resourceType): self
    {
        return new self($data, $resourceType);
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(Field $field, $default = null)
    {
        $fieldName = $field->getName();

        if ($field instanceof Property) {
            return $this->properties[$fieldName] ?? $default;
        }

        if (!$field instanceof Attribute) {
            throw new LogicException('Unsupported type of field');
        }

        return $this->values->get($field, $default);
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

    public function getCodeOrIdentifier(): ?string
    {
        return $this->codeOrIdentifier;
    }

    public function setCodeOrIdentifier(string $codeOrIdentifier): self
    {
        $this->properties[$this->getCodeOrIdentifierFieldName()] = $codeOrIdentifier;

        return $this;
    }

    public function getCodeOrIdentifierFieldName(): string
    {
        return $this->resourceType !== 'product' ? 'code' : 'identifier';
    }

    public function diff(Resource $resource): \AkeneoEtl\Domain\Resource
    {
        $propertiesDiff = [];
        foreach ($this->properties as $name => $value) {
            if (array_key_exists($name, $resource->properties) === false ||
                $value !== $resource->properties[$name]) {
                $propertiesDiff[$name] = $value;
            }
        }

        $identifierFieldName = $this->getCodeOrIdentifierFieldName();
        if (array_key_exists($identifierFieldName, $propertiesDiff) === false) {
            $propertiesDiff[$identifierFieldName] = $this->codeOrIdentifier;
        }

        $diff = new self($propertiesDiff, $this->resourceType);
        $diff->values = $this->values->diff($resource->values);

        return $diff;
    }

    public function isChanged(): bool
    {
        return $this->isChanged;
    }

    public function toArray(): array
    {
        $data = array_merge(
            [$this->getCodeOrIdentifierFieldName() => $this->codeOrIdentifier],
            $this->properties
        );

        if ($this->values->count() > 0) {
            $data['values'] = $this->values->toArray();
        }

        return $data;
    }

    public function __clone()
    {
        // Force a copy of this->object, otherwise
        // it will point to same object.
        $this->values = clone $this->values;
    }
}
