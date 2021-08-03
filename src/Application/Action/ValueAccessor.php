<?php

namespace AkeneoEtl\Application\Action;

class ValueAccessor
{
    /**
     * @param mixed $default
     * @return mixed
     */
    public function getValueByOptions(array $item, array $options, $default = null)
    {
        $field = $options['field'] ?? null;

        if (isset($item[$field]) === true) {
            return $item[$field];
        }

        $scope = $options['scope'] ?? null;
        $locale = $options['locale'] ?? null;

        foreach ($item['values'][$field] ?? [] as $attribute) {
            if ($attribute['scope'] === $scope &&
                $attribute['locale'] === $locale) {
                return $attribute['data'] ?? $default;
            }
        }

        return $default;
    }
}
