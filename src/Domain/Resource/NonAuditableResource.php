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
    private array $properties;

    private ValueCollection $values;

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
        $this->values = ValueCollection::fromArray($data['values'] ?? [], $resourceType);

        unset($data['values']);
        $this->properties = $data;
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
    public function set(Field $field, $newValue): Resource
    {
        // @todo: check if new value and old value are different

        $this->isChanged = true;

        $name = $field->getName();

        if ($field instanceof Property) {

            if ($newValue === null || $this->isScalarOrSimpleArray($newValue) === true) {
                $this->properties[$name] = $newValue;
            } elseif ($this->isObjectLikeArray($newValue) === true) {

                foreach ($newValue as $key => $valueElement) {
                    $this->properties[$name][$key] = $valueElement;
                }

            } else {
                $this->properties[$name] = $newValue;
            }


            return $this;
        }

        if (!$field instanceof Attribute) {
            throw new LogicException('Unsupported type of field');
        }

        $this->values->set($field, $newValue);

        return $this;
    }

    private function isObjectLikeArray(array $data): bool
    {
        return is_string(array_key_first($data));
    }

    /**
     * @param mixed $data
     */
    private function isScalarOrSimpleArray($data): bool
    {
        if (is_scalar($data) === true) {
            return true;
        }

        if (is_array($data) === true) {
            return $this->isObjectLikeArray($data) === false;
        }

        return false;
    }

    public function addTo(Field $field, array $newValue): Resource
    {
        $this->isChanged = true;

        if ($field instanceof Property) {
            $existingValue = $this->properties[$field->getName()];

            $this->properties[$field->getName()] = array_unique(array_merge($existingValue, $newValue));

            return $this;
        }

        if (!$field instanceof Attribute) {
            throw new LogicException('Unsupported type of field');
        }

        $this->values->addTo($field, $newValue);

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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): Resource
    {
        $this->properties[$this->getCodeFieldName()] = $code;

        return $this;
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
        foreach (array_keys($this->properties) as $propertyName) {
            yield Property::create($propertyName);
        }

        foreach ($this->values->attributes() as $attribute) {
            yield $attribute;
        }
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

    public function __clone()
    {
        $this->values = clone $this->values;
    }
}
