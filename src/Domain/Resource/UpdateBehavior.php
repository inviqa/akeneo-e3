<?php

namespace AkeneoEtl\Domain\Resource;

use AkeneoEtl\Domain\ArrayHelper;
use AkeneoEtl\Domain\Exception\TransformException;
use LogicException;

/**
 * Update Behavior rules
 *
 * @see https://api.akeneo.com/documentation/update.html#update-behavior
 *
 * Rules:
 *      Rule 1: If the value is an object, it will be merged with the old value.
 *      Rule 2: If the value is not an object, it will replace the old value.
 *      Rule 3: For non-scalar values (objects and arrays) data types must match.
 *      Rule 4: Any data in non specified properties will be left untouched.
 */
class UpdateBehavior
{
    private ArrayHelper $arrayHelper;

    public function __construct()
    {
        $this->arrayHelper = new ArrayHelper();
    }

    /**
     * @param mixed $patch
     */
    public function patch(array &$original, string $fieldName, $patch): void
    {
        $this->patchRecursive(
            $original,
            $fieldName,
            $patch,
            function (array &$original, string $fieldName, $patch) {
                $original[$fieldName] = $patch;
            }
        );
    }

    public function addTo(array &$original, string $fieldName, array $itemsToAdd): void
    {
        $this->patchRecursive(
            $original,
            $fieldName,
            $itemsToAdd,
            function (array &$original, string $fieldName, $patch) {
                $before = $original[$fieldName] ?? [];
                $original[$fieldName] = array_unique(array_merge($before, $patch));
            }
        );
    }

    /**
     * @param mixed $patch
     */
    public function patchRecursive(array &$original, string $fieldName, $patch, callable $valuePatcher): void
    {
        // Update Behavior: Rule 3 (validation on data types)
        // For non-scalar values (objects and arrays) data types must match.
        // Throw an exception that can be processes by caller.
        if (array_key_exists($fieldName, $original) === true &&
            $this->arrayHelper->haveMatchingTypes($patch, $original[$fieldName] ?? null) === false) {
            throw new TransformException(sprintf('New value for the field %s does not match its type', $fieldName), true);
        }

        // Update Behavior: Rule 2 (non object update)
        // If the value is not an object, it will replace the old value.
        if ($patch === null || $this->arrayHelper->isScalarOrSimpleArray($patch) === true) {
            //$original[$fieldName] = $patch;

            $valuePatcher($original, $fieldName, $patch);

            return;
        }

        // Update Behavior: Rule 1 (object update)
        // If the value is an object, it will be merged with the old value.
        foreach ($patch as $key => $value) {
            if (array_key_exists($fieldName, $original) === false) {
                $original[$fieldName] = [];
            }

            $this->patchRecursive($original[$fieldName], $key, $value, $valuePatcher);
        }
    }

//    public function addTo(array &$original, string $fieldName, array $itemsToAdd): void
//    {
//        if ($this->arrayHelper->isSimpleArray($itemsToAdd) === true) {
//            if (isset($original[$fieldName]) && $this->arrayHelper->isSimpleArray($original[$fieldName]) === false) {
//                throw new LogicException(sprintf('%s must be an array for using with `add`', $fieldName));
//            }
//
//            $before = $original[$fieldName] ?? [];
//            // @todo: check if types match
//            $original[$fieldName] = array_unique(array_merge($before, $itemsToAdd));
//
//            return;
//        }
//
//        foreach ($itemsToAdd as $key => $value) {
//            if (array_key_exists($fieldName, $original) === false) {
//                $original[$fieldName] = [];
//            }
//
//            $this->addTo($original[$fieldName], $key, $value);
//        }
//    }
}
