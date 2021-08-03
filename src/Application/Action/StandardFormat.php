<?php

namespace AkeneoEtl\Application\Action;

class StandardFormat
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function fromValues(array $values): self
    {
        return new self(['values' => $values]);
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
}
