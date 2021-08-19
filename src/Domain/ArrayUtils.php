<?php

namespace AkeneoEtl\Domain;

class ArrayUtils
{
    public static function isLikeObject(array $array): bool
    {
        return is_string(array_key_first($array));
    }

    /**
     * @param mixed $value
     */
    public static function isScalarOrSimpleArray($value): bool
    {
        if (is_scalar($value) === true) {
            return true;
        }

        if (is_array($value) === true) {
            return self::isLikeObject($value) === false;
        }

        return false;
    }

    /**
     * @param mixed $value
     */
    public static function isSimpleArray($value): bool
    {
        return is_array($value) === true && self::isLikeObject($value) === false;
    }
}
