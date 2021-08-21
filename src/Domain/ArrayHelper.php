<?php

declare(strict_types=1);

namespace AkeneoE3\Domain;

final class ArrayHelper
{
    /**
     * @param mixed|null $value
     */
    public function isLikeObject($value): bool
    {
        // Check only first key - it should be sufficient
        return is_array($value) === true && is_string(array_key_first($value));
    }

    /**
     * @param mixed $value
     */
    public function isSimpleArray($value): bool
    {
        return is_array($value) === true && $this->isLikeObject($value) === false;
    }

    /**
     * @param mixed $value
     */
    public function isScalarOrSimpleArray($value): bool
    {
        if (is_scalar($value) === true) {
            return true;
        }

        return $this->isSimpleArray($value) === true;
    }

    /**
     * @param mixed $value
     */
    public function isScalarOrNull($value): bool
    {
        return $value === null || is_scalar($value) === true;
    }

    /**
     * @param mixed|null $value
     */
    public function isSimpleArrayOrLikeObject($value): bool
    {
        return $this->isSimpleArray($value) === true || $this->isLikeObject($value) === true;
    }

    /**
     * @param mixed|null $value1
     * @param mixed|null $value2
     */
    public function haveMatchingTypes($value1, $value2): bool
    {
        if ($this->isSimpleArray($value1) && $this->isSimpleArray($value2)) {
            return true;
        }

        if ($this->isLikeObject($value1) && $this->isLikeObject($value2)) {
            return true;
        }

        if ($this->isScalarOrNull($value1) && $this->isScalarOrNull($value2)) {
            return true;
        }

        return false;
    }

    public function merge(?array $array1, array $array2): array
    {
        return array_values(array_unique(array_merge($array1 ?? [], $array2)));
    }

    public function subtract(?array $array1, array $array2): array
    {
        return array_values(array_diff($array1 ?? [], $array2));
    }
}
