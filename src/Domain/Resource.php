<?php

namespace AkeneoEtl\Domain;

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

        if (isset($this->data[$fieldName]) === true) {
            return $this->data[$fieldName];
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
    public function set(Field $field, $newValue, bool $isAttribute): void
    {
        // @todo: check if new value and old value are different

        $this->isChanged = true;

        $this->changes = array_merge_recursive($this->getPatch($field, $newValue, $isAttribute));
    }

    public function isAttribute(string $field): bool
    {
        // @todo: hardcode all top-level fields of akeneo api?
        return isset($this->data[$field]) === false;
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
    private function getPatch(Field $field, $newValue, bool $isAttribute): array
    {
        if ($isAttribute === true) {
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

        return [$field->getName() => $newValue];
    }
}
