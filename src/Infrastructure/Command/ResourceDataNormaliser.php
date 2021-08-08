<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Command;

class ResourceDataNormaliser
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function normalise($value)
    {
        if (is_array($value) === true && $this->isObjectLikeArray($value) === true) {
            return '~not supported~';
        }

        if (is_array($value) === true) {
            return implode(', ', $value);
        }

        if (is_scalar($value) === false) {
            return '~not supported~';
        }

        return $value;
    }

    private function isObjectLikeArray(array $data): bool
    {
        return is_string(array_key_first($data));
    }
}
