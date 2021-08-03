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
    public function getByOptions(array $options, $default = null)
    {
        $field = $options['field'] ?? null;

        if (isset($this->data[$field]) === true) {
            return $this->data[$field];
        }

        $scope = $options['scope'] ?? null;
        $locale = $options['locale'] ?? null;

        foreach ($this->data['values'][$field] ?? [] as $attribute) {
            if ($attribute['scope'] === $scope &&
                $attribute['locale'] === $locale) {
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
    public function makeValueArray(array $options, $newValue, bool $isAttribute)
    {
        // @todo: what if field is not set?
        if ($isAttribute === true) {
            return [
                'values' => [
                    $options['field'] => [
                        [
                            'scope' => $options['scope'] ?? null,
                            'locale' => $options['locale'] ?? null,
                            'data' => $newValue
                        ]
                    ]
                ]
            ];
        }

        return [$options['field'] => $newValue];
    }
}
