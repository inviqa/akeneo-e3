<?php

namespace AkeneoEtl\Domain;

use LogicException;

class Resource
{
    private array $data;

    private string $resourceType;

    private array $changes = [];

    private bool $isChanged = false;

    private function __construct(array $data, string $resourceType)
    {
        $this->data = $data;
        $this->resourceType = $resourceType;
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
            return $this->data[$fieldName] ?? $default;
        }

        if (!$field instanceof Attribute) {
            throw new LogicException('Unsupported type of field');
        }

        foreach ($this->data['values'][$fieldName] ?? [] as $attribute) {
            if ($attribute['scope'] === $field->getScope() &&
                $attribute['locale'] === $field->getLocale()) {
                return $attribute['data'] ?? $default;
            }
        }

        return $default;
    }

    /**
     * @param mixed $newValue
     */
    public function set(Field $field, $newValue): void
    {
        // @todo: check if new value and old value are different

        $this->isChanged = true;

        $this->changes = array_merge_recursive($this->getPatch($field, $newValue));
    }

    public function getCodeOrIdentifier(): string
    {
        return $this->resourceType !== 'product' ?
            $this->data['code'] :
            $this->data['identifier'];
    }

    public function changes(): self
    {
        return self::fromArray($this->changes, $this->resourceType);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function isChanged(): bool
    {
        return $this->isChanged;
    }

    /**
     * @param mixed $newValue
     */
    private function getPatch(Field $field, $newValue): array
    {
        if ($field instanceof Property) {
            return [$field->getName() => $newValue];
        }

        if (!$field instanceof Attribute) {
            throw new LogicException('Unsupported type of field');
        }

        return [
            'values' => [
                $field->getName() => [
                    [
                        'scope' => $field->getScope(),
                        'locale' => $field->getLocale(),
                        'data' => $newValue
                    ]
                ]
            ]
        ];
    }
}
