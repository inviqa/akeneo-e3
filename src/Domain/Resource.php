<?php

namespace AkeneoEtl\Domain;

class Resource
{
    private array $data;

    private string $resourceType;

    private function __construct(array $data, string $resourceType)
    {
        $this->data = $data;
        $this->resourceType = $resourceType;
    }

    public static function fromArray(array $data, string $resourceType): self
    {
        return new self($data, $resourceType);
    }

    public static function fromValues(array $values, string $resourceType): self
    {
        return new self(['values' => $values], $resourceType);
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

    public function isAttribute(string $field): bool
    {
        // @todo: hardcode all top-level fields of akeneo api?
        return isset($this->data[$field]) === false;
    }

    /**
     * @param mixed $newValue
     */
    public function makeValueArray(Field $field, $newValue, bool $isAttribute): array
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

    public function toArray(): array
    {
        return $this->data;
    }

    public function getCodeOrIdentifier(): string
    {
        return $this->resourceType !== 'product' ?
            $this->data['code'] :
            $this->data['identifier'];
    }
}
