<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

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
     * @param mixed|null $value
     */
    public function isSimpleArrayOrLikeObject($value): bool
    {
        return $this->isSimpleArray($value) === true || $this->isLikeObject($value) === true;
    }
}
