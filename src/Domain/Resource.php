<?php

declare(strict_types=1);

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

        $this->codeOrIdentifier = (string)$data[$idFieldName];
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
    public function get(Field $field)
    {
        $fieldName = $field->getName();

        if ($field instanceof Property) {

            if (array_key_exists($fieldName, $this->properties) === false) {
                throw new LogicException(sprintf('Field %s is not present in data.', $fieldName));
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

        $identifierFieldName = $this->getCodeOrIdentifierFieldName();
        if (array_key_exists($identifierFieldName, $propertiesDiff) === false) {
            $propertiesDiff[$identifierFieldName] = $this->codeOrIdentifier;
        }

        $diff = new self($propertiesDiff, $this->resourceType);
        $diff->values = $this->values->diff($resource->values);

        return $diff;
    }

    public function merge(Resource $resource): self
    {
        $propertiesMerge = $this->properties;
        foreach ($resource->properties as $name => $value) {
            if (is_array($value) === true && $this->isObjectLikeArray($value) === true) {
                $value = array_merge($this->properties[$name], $value);
            }

            $propertiesMerge[$name] = $value;
        }

        $merge = new self($propertiesMerge, $this->resourceType);
        $merge->values = $this->values->merge($resource->values);

        return $merge;
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

    private function isObjectLikeArray(array $data): bool
    {
        return is_string(array_key_first($data));
    }

    public function __clone()
    {
        $this->values = clone $this->values;
    }
}
